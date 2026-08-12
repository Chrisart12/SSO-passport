<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Autorisation — {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, sans-serif;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .app-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #111;
            margin-bottom: 0.25rem;
        }
        .subtitle {
            color: #6b7280;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }
        .scopes {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .scopes h3 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 0.75rem;
        }
        .scope-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            color: #374151;
            padding: 0.25rem 0;
        }
        .scope-item::before {
            content: '✓';
            color: #10b981;
            font-weight: 700;
        }
        .no-scopes {
            font-size: 0.9rem;
            color: #9ca3af;
            font-style: italic;
        }
        .actions {
            display: flex;
            gap: 0.75rem;
        }
        .btn {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.15s;
        }
        .btn:hover { opacity: 0.85; }
        .btn-approve {
            background: #2563eb;
            color: white;
        }
        .btn-deny {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="app-name">{{ $client->name }}</div>
        <div class="subtitle">
            souhaite accéder à votre compte <strong>{{ config('app.name') }}</strong>
        </div>

        @if(count($scopes) > 0)
            <div class="scopes">
                <h3>Permissions demandées</h3>
                @foreach($scopes as $scope)
                    <div class="scope-item">{{ $scope->description }}</div>
                @endforeach
            </div>
        @else
            <div class="scopes">
                <p class="no-scopes">Aucune permission spécifique demandée.</p>
            </div>
        @endif

        {{-- Approuver --}}
        <div class="actions">
            <form method="POST" action="{{ route('passport.authorizations.approve') }}" style="flex:1">
                @csrf
                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                <button type="submit" class="btn btn-approve">Autoriser</button>
            </form>

            {{-- Refuser --}}
            <form method="POST" action="{{ route('passport.authorizations.deny') }}" style="flex:1">
                @csrf
                @method('DELETE')
                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                <button type="submit" class="btn btn-deny">Refuser</button>
            </form>
        </div>
    </div>
</body>
</html>