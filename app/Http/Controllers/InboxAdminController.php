<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\InboxReplyMail;
use App\Models\InboxConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InboxAdminController extends Controller
{
    /**
     * INBOX: non archiviati, non nel cestino
     */
    public function index(Request $request)
    {
        $q = InboxConversation::query()
            ->whereNull('archived_at');

        // filtri
        if ($request->get('filter') === 'unread') $q->whereNull('read_at');
        if ($request->get('filter') === 'read')   $q->whereNotNull('read_at');
        if ($request->filled('source'))           $q->where('source', $request->get('source')); // contact|quote

        if ($request->filled('search')) {
            $s = trim($request->get('search'));
            $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%")
                  ->orWhere('user_message', 'like', "%{$s}%");
            });
        }

        $conversations = $q
            ->orderByRaw('read_at IS NULL DESC') // non lette sopra
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.inbox.index', compact('conversations'));
    }

    /**
     * ARCHIVIO: archiviati, non nel cestino
     */
    public function archive(Request $request)
    {
        $q = InboxConversation::query()
            ->whereNotNull('archived_at');

        if ($request->filled('source')) $q->where('source', $request->get('source'));

        $conversations = $q->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.inbox.archive', compact('conversations'));
    }

    /**
     * CESTINO: solo soft deleted
     */
    public function trash(Request $request)
    {
        $q = InboxConversation::onlyTrashed();

        if ($request->filled('source')) $q->where('source', $request->get('source'));

        $conversations = $q->orderByDesc('deleted_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.inbox.trash', compact('conversations'));
    }

    /**
     * SHOW: mostra il messaggio (anche se in cestino => withTrashed in rotta)
     * Se non è nel cestino e non è letto, segna letto automaticamente.
     */
    public function show(InboxConversation $conversation)
    {
        if (!$conversation->trashed() && is_null($conversation->read_at)) {
            $conversation->update(['read_at' => now()]);
        }

        return view('admin.inbox.show', compact('conversation'));
    }

    /**
     * Segna come LETTO
     */
    public function markRead(InboxConversation $conversation)
    {
        $conversation->update(['read_at' => now()]);
        return back()->with('success', 'Segnato come letto.');
    }

    /**
     * Segna come NON LETTO
     */
    public function markUnread(InboxConversation $conversation)
    {
        $conversation->update(['read_at' => null]);
        return back()->with('success', 'Segnato come non letto.');
    }

    /**
     * Archivia (da inbox)
     */
    public function archiveOne(InboxConversation $conversation)
    {
        $conversation->update(['archived_at' => now()]);
        return back()->with('success', 'Archiviato.');
    }

    /**
     * Ripristina da archivio (torna in inbox)
     */
    public function unarchiveOne(InboxConversation $conversation)
    {
        $conversation->update(['archived_at' => null]);
        return back()->with('success', 'Ripristinato in Inbox.');
    }

    /**
     * Sposta nel cestino (soft delete)
     */
    public function moveToTrash(InboxConversation $conversation)
    {
        $conversation->delete();
        return back()->with('success', 'Spostato nel cestino.');
    }

    /**
     * Ripristina dal cestino
     */
    public function restore($conversationId)
    {
        $conversation = InboxConversation::onlyTrashed()->findOrFail($conversationId);
        $conversation->restore();

        return back()->with('success', 'Ripristinato dal cestino.');
    }

    /**
     * Eliminazione definitiva singola (solo dal cestino)
     */
    public function forceDelete($conversationId)
    {
        $conversation = InboxConversation::onlyTrashed()->findOrFail($conversationId);
        $conversation->forceDelete();

        return back()->with('success', 'Eliminato definitivamente.');
    }

    /**
     * SVUOTA CESTINO (eliminazione definitiva massiva)
     */
    public function emptyTrash()
    {
        InboxConversation::onlyTrashed()->forceDelete();

        return back()->with('success', 'Cestino svuotato.');
    }

    /**
     * Risposta via email:
     * - salva reply_* e replied_at
     * - invia mail con citazione del testo utente
     */
    public function reply(Request $request, InboxConversation $conversation)
    {
        $data = $request->validate([
            'reply_subject' => ['nullable', 'string', 'max:180'],
            'reply_body'    => ['required', 'string', 'max:10000'],
        ]);

        $subject = $data['reply_subject'] ?: $this->defaultReplySubject($conversation);

        $conversation->update([
            'reply_subject'  => $subject,
            'reply_body'     => $data['reply_body'],
            'reply_to_email' => $conversation->email,
            'replied_at'     => now(),
            'read_at'        => $conversation->read_at ?? now(),
        ]);

        Mail::to($conversation->email)->send(
            new InboxReplyMail($conversation, $data['reply_body'], $subject)
        );

        return back()->with('success', 'Risposta inviata via email.');
    }

    private function defaultReplySubject(InboxConversation $conversation): string
    {
        $base = trim((string) $conversation->subject);
        if ($base === '') $base = 'Messaggio dal sito';

        return str_starts_with(strtolower($base), 're:')
            ? $base
            : 'Re: ' . $base;
    }
}
