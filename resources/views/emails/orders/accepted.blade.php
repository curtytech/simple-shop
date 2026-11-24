<h1>Encomenda aceita</h1>

<p>Olá, {{ $order->client->name }}.</p>

<p>A loja <strong>{{ $order->store->name }}</strong> aceitou sua encomenda.</p>

<p>
    Produto: <strong>{{ $order->product->name }}</strong><br>
    Quantidade: {{ $order->quantity }}<br>
    Total: R$ {{ number_format((float) $order->total_price, 2, ',', '.') }}
</p>

<p>Agora você pode abrir a loja e realizar o pagamento da encomenda.</p>

<p><a href="{{ route('store.show', ['slug' => $order->store->slug]) }}">Abrir loja</a></p>
