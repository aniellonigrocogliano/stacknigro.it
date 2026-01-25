<p>Ciao {{ $conversation->name }},</p>

<p>{!! nl2br(e($replyBody)) !!}</p>

<hr>

<p style="color:#666; font-size: 13px;">
  <strong>Messaggio originale:</strong><br>
  Da: {{ $conversation->name }} ({{ $conversation->email }})<br>
  Inviato: {{ optional($conversation->created_at)->format('d/m/Y H:i') }}<br><br>

  <blockquote style="margin: 8px 0; padding-left: 12px; border-left: 3px solid #ddd;">
    {!! nl2br(e($conversation->user_message)) !!}
  </blockquote>
</p>
