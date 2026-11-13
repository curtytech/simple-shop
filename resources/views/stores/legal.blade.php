<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document === 'privacy' ? 'Política de Privacidade' : 'Termos de Uso' }} - {{ $store->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-800">
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 py-5">
            <a href="{{ route('store.show', $store->slug) }}" class="text-xl font-bold text-gray-900">
                {{ $store->name }}
            </a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-10">
        <article class="bg-white rounded-xl shadow-sm p-6 sm:p-10">
            @if($document === 'privacy')
                <h1 class="text-3xl font-bold mb-2">Política de Privacidade</h1>
                <p class="text-sm text-gray-500 mb-8">Loja {{ $store->name }} · Versão vigente</p>

                <div class="space-y-7 leading-7">
                    <section>
                        <h2 class="text-xl font-semibold mb-2">1. Sobre esta política</h2>
                        <p>Esta política explica como a loja {{ $store->name }} trata dados pessoais quando você visita esta página, cria uma conta, usa o carrinho ou realiza uma compra. A plataforma ShopYou fornece a tecnologia da loja; o lojista é o contato para questões sobre os dados tratados no contexto da venda. O papel de cada parte pode variar conforme a operação e a legislação aplicável.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold mb-2">2. Dados que podem ser tratados</h2>
                        <p>Podemos tratar os dados informados no cadastro e na conta, como nome, e-mail, telefone e endereço (incluindo número, CEP, cidade, estado, país e ponto de referência), além dos dados necessários para autenticação. Também são tratados os itens do carrinho, os produtos comprados, o valor e o status do pedido, identificadores e informações da transação recebidos do Mercado Pago e informações técnicas necessárias ao funcionamento da sessão e do site.</p>
                        <p class="mt-2">O tema visual escolhido pode ser salvo no armazenamento local do seu navegador. O pagamento é processado pelo Mercado Pago; os dados de pagamento são tratados por esse provedor de acordo com os próprios termos e avisos de privacidade. Não informe dados de cartão diretamente à loja por canais não oficiais.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold mb-2">3. Para que usamos os dados</h2>
                        <p>Os dados são usados para criar e manter sua conta, permitir acesso ao carrinho, processar e acompanhar pedidos e pagamentos, comunicar informações sobre a compra, prestar suporte, prevenir fraudes e cumprir obrigações legais. O tratamento deve se limitar ao necessário para essas finalidades.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold mb-2">4. Compartilhamento</h2>
                        <p>Os dados necessários podem ser compartilhados com a loja {{ $store->name }} para atendimento e gestão do pedido, com o Mercado Pago para processamento e confirmação do pagamento, e com prestadores técnicos envolvidos na hospedagem e operação do serviço. Também poderão ser compartilhados quando exigido por lei ou por autoridade competente. Cada fornecedor pode ter sua própria política de privacidade.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold mb-2">5. Retenção e proteção</h2>
                        <p>Os dados são mantidos pelo período necessário às finalidades informadas e ao cumprimento de obrigações legais, regulatórias e de defesa de direitos. A loja e os prestadores envolvidos devem adotar medidas técnicas e administrativas apropriadas para proteger os dados. Nenhum sistema conectado à internet pode ter sua segurança garantida de forma absoluta.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold mb-2">6. Seus direitos e contato</h2>
                        <p>Nos termos da Lei Geral de Proteção de Dados (Lei nº 13.709/2018), você pode solicitar confirmação e acesso aos dados, correção, anonimização, bloqueio ou eliminação quando cabível, informações sobre compartilhamento, portabilidade nos termos da regulamentação e revogação do consentimento quando essa for a base do tratamento. A exclusão poderá ser limitada quando houver obrigação legal de retenção.</p>
                        <p class="mt-2">Para exercer seus direitos ou tirar dúvidas, entre em contato com a loja pelo telefone informado abaixo ou pelos canais exibidos na página da loja. A solicitação poderá exigir confirmação da sua identidade.</p>
                        @if($store->celphone)
                            <ul class="mt-2">
                                <li>Telefone: {{ $store->celphone }}</li>
                            </ul>
                        @else
                            <p class="mt-2">Consulte os canais de contato exibidos na página da loja.</p>
                        @endif
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold mb-2">7. Alterações</h2>
                        <p>Esta política pode ser atualizada para refletir mudanças no serviço ou nas exigências legais. A versão vigente estará disponível nesta página.</p>
                    </section>
                </div>
            @else
                <h1 class="text-3xl font-bold mb-2">Termos de Uso</h1>
                <p class="text-sm text-gray-500 mb-8">Loja {{ $store->name }} · Versão vigente</p>

                <div class="space-y-7 leading-7">
                    <section>
                        <h2 class="text-xl font-semibold mb-2">1. Aplicação destes termos</h2>
                        <p>Estes termos regem o uso da página da loja {{ $store->name }} na plataforma ShopYou, incluindo navegação, cadastro, carrinho e compras. Ao utilizar esses recursos, você declara que leu estes termos. Se não concordar, não utilize as funções de cadastro e compra.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold mb-2">2. Conta e informações</h2>
                        <p>Para comprar, pode ser necessário criar uma conta e fornecer informações verdadeiras, completas e atualizadas. Você é responsável por manter a confidencialidade da senha e por avisar a loja sobre uso não autorizado da conta. Não compartilhe sua senha com terceiros.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold mb-2">3. Produtos, pedidos e pagamento</h2>
                        <p>As informações de produtos, preços, disponibilidade, entrega, trocas e atendimento são apresentadas ou definidas pelo lojista, que deve mantê-las claras e atualizadas. A inclusão de um produto no carrinho não garante sua reserva ou a confirmação do pedido. A compra depende da confirmação do pedido e do pagamento, quando aplicável.</p>
                        <p class="mt-2">O pagamento disponível nesta loja é processado pelo Mercado Pago, sujeito às condições e à disponibilidade do próprio provedor. O status do pedido pode ser consultado na área de compras da conta. Em caso de divergência, entre em contato com o lojista.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold mb-2">4. Entrega, cancelamentos e direitos do consumidor</h2>
                        <p>O lojista é o responsável por informar as condições de entrega, prazos, custos e canais de atendimento relativos aos produtos vendidos. Nada nestes termos limita os direitos assegurados pela legislação brasileira, inclusive pelo Código de Defesa do Consumidor. Para solicitações de cancelamento, troca, devolução ou suporte, contate o lojista pelos canais da loja.</p>
                        @if($store->celphone)
                            <ul class="mt-2">
                                <li>Telefone: {{ $store->celphone }}</li>
                            </ul>
                        @endif
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold mb-2">5. Uso aceitável</h2>
                        <p>Você concorda em não usar a loja para fins ilícitos, não tentar comprometer sua segurança ou funcionamento, não fornecer dados de terceiros sem autorização e não interferir no uso do serviço por outras pessoas. O acesso poderá ser limitado quando necessário para proteger a loja, os usuários ou cumprir a lei.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold mb-2">6. Plataforma e disponibilidade</h2>
                        <p>A ShopYou fornece recursos tecnológicos para exibição da loja e suporte ao fluxo de compra. A disponibilidade pode sofrer interrupções para manutenção ou por fatores fora do controle da plataforma. A relação de compra dos produtos é estabelecida entre o consumidor e o lojista, sem prejuízo das responsabilidades previstas em lei para cada parte.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold mb-2">7. Privacidade e atualizações</h2>
                        <p>O tratamento de dados pessoais está descrito na <a class="text-blue-700 underline" href="{{ route('store.privacy-policy', $store->slug) }}">Política de Privacidade</a>. Estes termos podem ser atualizados; a versão vigente será publicada nesta página.</p>
                    </section>
                </div>
            @endif

            <nav class="mt-10 flex flex-wrap gap-4">
                <a href="{{ route('store.show', $store->slug) }}" class="text-blue-700 hover:underline">Voltar à loja</a>
                @if($document === 'privacy')
                    <a href="{{ route('store.terms', $store->slug) }}" class="text-blue-700 hover:underline">Termos de Uso</a>
                @else
                    <a href="{{ route('store.privacy-policy', $store->slug) }}" class="text-blue-700 hover:underline">Política de Privacidade</a>
                @endif
            </nav>
        </article>
    </main>
</body>
</html>
