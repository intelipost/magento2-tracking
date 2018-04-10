# Manual de Uso: Módulo Tracking Intelipost

[![logo](https://image.prntscr.com/image/E8AfiBL7RQKKVychm7Aubw.png)](http://www.intelipost.com.br)

## Introdução

O módulo Tracking é responsável por receber as ocorrências de transporte das entregas (pedido em trânsito, entregue, cancelado e etc) e registrá-las nos pedidos do Magento.
Além disso, o módulo é responsável por construir o Link de Rastreamento, que pode ser disponibilizado para o cliente acompanhar a entrega em tempo real.

Este manual foi divido em três partes:

  - [Instalação](#instalação): Onde você econtrará instruções para instalar nosso módulo.
  - [Configurações](#configurações): Onde você encontrará o caminho para realizar as configurações e explicações de cada uma delas.
  - [Uso](#uso): Onde você encontrará a maneira de utilização de cada uma das funcionalidades.

-----
## Instalação
> É recomendado que você tenha um ambiente de testes para validar alterações e atualizações antes de atualizar sua loja em produção.

> A instalação do módulo é feita utilizando o Composer. Para baixar e instalar o Composer no seu ambiente acesse https://getcomposer.org/download/ e caso tenha dúvidas de como utilizá-lo consulte a [documentação oficial do Composer](https://getcomposer.org/doc/).

Navegue até o diretório raíz da sua instalação do Magento 2 e execute os seguintes comandos:

```
bin/composer require intelipost/magento2-tracking   // Faz a requisição do módulo da Intelipost
bin/magento module:enable Intelipost_Tracking       // Ativa o módulo
bin/magento setup:upgrade                           // Registra a extensão
bin/magento setup:di:compile                        // Recompila o projeto Magento
```
-----

## Configurações
Para acessar o menu de configurações, basta seguir os seguintes passos:

No menu à esquerda, acessar **Stores** -> **Configuration** -> **Intelipost** -> **Tracking**:

![b1](https://s3.amazonaws.com/email-assets.intelipost.net/integracoes/tr1.gif)


### Configurações Básicas

**Active**: 
Se o módulo está ativo, isto é, apto a receber notificações de entrega da Intelipost.

**Client Id**: O número de identificação da sua conta na Intelipost. Necessário para criar o Link de Rastreamento.

**Track pre shipment events?**
Os eventos pré despacho devem ser rastreados? Se sim, abrirão 3 novas configurações:
- **Intelipost Created**: Selecionar o Status Magento que o pedido deve assumir quando o pedido estiver "Criado" na Intelipost.
- **Intelipost Ready For Shipment**: Selecionar o Status Magento que o pedido deve assumir quando o pedido estiver Pronto "Para Envio" na Intelipost.
- **Intelipost Shipped**: Selecionar o Status Magento que o pedido deve assumir quando o pedido estiver Pronto "Despachado" na Intelipost.

**Track post shipment events?**
Os eventos pré despacho devem ser rastreados? Se sim, abrirão 5 novas configurações:
- **Intelipost In Transit**: Selecionar o Status Magento que o pedido deve assumir quando o pedido estiver "Em Trânsito" na Intelipost.
- **Intelipost To be Delivered**: Selecionar o Status Magento que o pedido deve assumir quando o pedido estiver "Saiu para Entrega" na Intelipost.
- **Intelipost Delivered**: Selecionar o Status Magento que o pedido deve assumir quando o pedido estiver "Entregue" na Intelipost.
- **Intelipost Clarify Delivery Failed**: Selecionar o Status Magento que o pedido deve assumir quando o pedido estiver "Averiguar Falha na Entrega" na Intelipost.
- **Intelipost Delivery Failed**: Selecionar o Status Magento que o pedido deve assumir quando o pedido estiver "Falha na Entrega" na Intelipost.

**Create shipment after Intelipost Shipped**:
Se esta configuração for marcada como "Sim", sempre que o Magento receber uma notificação de que um pedido foi "Despachado", ele criará automaticamente uma entrega com todos os itens do pedido.

**Send shipment notification**:
Associado ao evento anterior, você pode configurar por aqui se as entregas geradas pela Intelipost devem enviar notificações aos clientes.
