<?php

namespace App\Http\Controllers;

use App\Mail\InboxReplyMail;
use App\Models\InboxConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InboxController extends Controller
{
    private function baseQuery()
    {
        return InboxConversation::query()->orderByDesc('created_at');
    }

    // Inbox: non archiviati e non nel cestino
    public function index()
    {
        $items = $this->baseQuery()
            ->whereNull('archived_at')
            ->whereNull('deleted_at')
            ->paginate(20);

        return view('admin.inbox.index', [
            'items' => $items,
            'folder' => 'inbox',
        ]);
    }

    // Archivio: archiviati e non nel cestino
    public function archive()
    {
        $items = $this->baseQuery()
            ->whereNotNull('archived_at')
            ->whereNull('deleted_at')
            ->paginate(20);

        return view('admin.inbox.index', [
            'items' => $items,
            'folder' => 'archive',
        ]);
    }

    // Cestino: soft deleted
    public function trash()
    {
        $items = $this->baseQuery()
            ->onlyTrashed()
            ->paginate(20);

        return view('admin.inbox.index', [
            'items' => $items,
            'folder' => 'trash',
        ]);
    }

    // Show: entra nel messaggio e marca come letto
    public function show(InboxConversation $conversation)
    {
        if (is_null($conversation->read_at)) {
            $conversation->update(['read_at' => now()]);
        }

        return view('admin.inbox.show', compact('conversation'));
    }

    public function markRead(InboxConversation $conversation)
    {
        $conversation->update(['read_at' => now()]);
        return back()->with('success', 'Segnato come letto.');
    }

    public function markUnread(InboxConversation $conversation)
    {
        $conversation->update(['read_at' => null]);
        return back()->with('success', 'Segnato come non letto.');
    }

    public function doArchive(InboxConversation $conversation)
    {
        $conversation->update(['archived_at' => now()]);
        return back()->with('success', 'Archiviato.');
    }

    public function unarchive(InboxConversation $conversation)
    {
        $conversation->update(['archived_at' => null]);
        return back()->with('success', 'Ripristinato in Inbox.');
    }

    // Soft delete => cestino
    public function destroy(InboxConversation $conversation)
    {
        $conversation->delete();
        return redirect()->route('admin.inbox.index')->with('success', 'Spostato nel cestino.');
    }

    // Reply: invia email e chiude conversazione
    public function reply(Request $request, InboxConversation $conversation)
    {
        if ($conversation->is_replied) {
            return back()->with('error', 'Hai già risposto: conversazione chiusa.');
        }

        $data = $request->validate([
            'reply_subject' => ['required', 'string', 'max:180'],
            'reply_body' => ['required', 'string', 'min:3'],
        ]);

        $to = $conversation->email;

        Mail::to($to)->send(new InboxReplyMail(
            subject: $data['reply_subject'],
            userMessage: (string) $conversation->user_message,
            replyBody: $data['reply_body'],
            senderName: 'Aniello Nigro Cogliano',
            senderEmail: 'aniello@stacknigro.it'
        ));

        $conversation->update([
            'reply_subject' => $data['reply_subject'],
            'reply_body' => $data['reply_body'],
            'reply_to_email' => $to,
            'replied_at' => now(),
            'read_at' => $conversation->read_at ?? now(),
        ]);

        return back()->with('success', 'Risposta inviata e conversazione chiusa.');
    }
}
