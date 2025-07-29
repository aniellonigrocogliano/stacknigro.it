@extends('backend.layouts.app')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h4 class="mb-4">Messaggi ricevuti</h4>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Email</th>
                        <th>Telefono</th>
                        <th>Messaggio</th>
                        <th>Data</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $msg)
                    <tr>
                        <td>{{ $msg->first_name }}</td>
                        <td>{{ $msg->last_name }}</td>
                        <td>{{ $msg->email }}</td>
                        <td>{{ $msg->phone }}</td>
                        <td>{{ Str::limit($msg->message, 50) }}</td>
                        <td>{{ $msg->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.messages.destroy', $msg->id) }}" method="POST" onsubmit="return confirm('Eliminare il messaggio?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($messages->isEmpty())
                        <tr><td colspan="7" class="text-center">Nessun messaggio presente.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection