##### -------------

Atue como um Engenheiro de Software Sênior especialista em Laravel e Livewire. Preciso implementar uma funcionalidade completa de cálculo e visualização de preços e prazos de frete utilizando a API v2 do **Melhor Envio** neste projeto.

Antes de escrever o código, analise a estrutura atual do meu projeto (`composer.json`, `config/services.php`, `app/Models` para identificar como os produtos, dimensões e o carrinho/pedido estão modelados, e `resources/views` para verificar o framework CSS utilizado, como Tailwind CSS).

Após analisar o contexto do projeto, implemente a solução seguindo rigorosamente estas etapas e padrões arquiteturais:

### 1. Configuração de Ambiente
- Atualize `config/services.php` adicionando a chave `melhor_envio` com suporte para `token`, `sandbox` (boolean) e `origin_cep`.
- Adicione as respectivas variáveis de exemplo no `.env.example` (e no `.env`, se existir).

### 2. Camada de Serviço (`App\Services\ShippingService`)
- Crie uma classe de serviço desacoplada utilizando o client HTTP nativo do Laravel (`Illuminate\Support\Facades\Http`).
- Implemente o método `calculate(string $destinationPostalCode, array $items, ?string $originPostalCode = null): array`.
- Alterne automaticamente a URL base entre Sandbox (`https://sandbox.melhorenvio.com.br/api/v2`) e Produção (`https://melhorenvio.com.br/api/v2`) com base na configuração.
- Envie o payload para `/me/shipment/calculate` mapeando `width`, `height`, `length`, `weight`, `insurance_value` e `quantity` de cada item.
- Utilize `Cache::remember` (com TTL de 2 horas) baseado no hash do CEP de destino e dos itens para evitar requisições repetidas à API externa.
- Filtre serviços retornados que possuam a chave `error` preenchida, trate falhas de HTTP/exceções com `Log::error` e retorne um array limpo ordenado pelo menor preço (`custom_price`).

### 3. Componente Reativo (Livewire)
- Crie um componente Livewire `ShippingCalculator` (verifique a versão do Livewire no `composer.json` para usar o namespace correto, ex: `App\Livewire` ou `App\Http\Livewire`).
- O componente deve receber os itens (ou um único produto) via propriedade pública/mount, validar o CEP (8 dígitos numéricos), exibir estado de carregamento (`wire:loading`) e listar as opções retornadas (logo da transportadora, nome do serviço, prazo em dias úteis e valor formatado em BRL).
- Permita que o usuário selecione uma das opções de frete retornadas e emita um evento Livewire (`$this->dispatch('shipping-selected', ...)`), além de salvar a opção escolhida na sessão para ser consumida no checkout/carrinho.

### 4. Qualidade e Padronização
- Utilize strict types (`declare(strict_types=1);`), tipagem forte nos parâmetros e retornos (PHP 8.3+), e siga as convenções PSR-12.
- Crie a view Blade responsiva utilizando as classes CSS já adotadas no projeto.
- Ao final, me informe exatamente onde e como devo incluir a tag do componente Livewire nas minhas views de produto ou carrinho, e quais campos de dimensão/peso preciso garantir no meu Model de Produto.

##### -------------

Lojas so podem ver a sua propria loja na grid do filament e nao podem editar dados de outras lojas 