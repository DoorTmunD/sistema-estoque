Features Futuras e Planejamento (Resumo Técnico)

Este arquivo serve como referência para o próprio ChatGPT em conversas futuras com o Danilo, recapitulando demandas já discutidas, ideias no radar e sugestões de robustez para o sistema de estoque em Laravel. Use linguagem direta/técnica para não perder tempo.

1. Auditoria (Activitylog)

Garantir que todos os módulos (produtos, categorias, fornecedores, estoque, movimentações) estão usando o Spatie Activitylog para logar criação, edição, deleção, e preferencialmente exportação de relatórios também.

O campo causer_id precisa estar preenchido sempre que houver um usuário logado executando a ação.

Criar view de Auditoria com filtros por data, usuário, ação, tipo (módulo), exportação CSV/PDF.

2. Upload de Anexos e Imagens

Implementar upload de imagem no cadastro de produtos (campo de foto no form, preview no index/show).

Permitir upload de arquivos em movimentação de estoque (nota fiscal, comprovante).

Permitir upload de documentos (contrato, CNPJ, etc) no cadastro de fornecedores.

3. Busca Global

Modal de busca universal (atalho Ctrl+K ou botão), pesquisando produtos, categorias, fornecedores, usuários, etc. Sugestão: busca Livewire ou Algolia.

Exibir resultados agrupados por tipo. Permitir navegar por teclado.

4. Permissões Granulares

Expandir roles: admin, gestor, operador, etc.

Definir permissões por módulo e por ação (visualizar, cadastrar, editar, excluir, exportar).

Tela de gerenciamento de roles/usuários.

5. Relatórios Exportáveis / Dashboard Personalizável

Exportação de listagens e relatórios em CSV/PDF. Opção de customizar filtros e colunas.

Permitir personalizar (ocultar/exibir) cards no dashboard por usuário.

6. Quick Actions & Atalhos

Implementar atalhos de teclado (novo produto, filtrar, exportar, etc).

Melhorar FAB para mostrar ações rápidas mais comuns. Tooltip com dica do atalho.

7. Notificações Inteligentes

Notificar usuário/admin sobre: produto abaixo do estoque mínimo, movimentação atípica, novos cadastros, etc.

(Opcional) Envio de alertas por email/telegram para admins.

8. Histórico Visual de Movimentações (Timeline)

Exibir timeline visual de todas movimentações (entrada/saída de estoque, edições relevantes), com filtro por período, usuário, tipo de ação.

9. Integração API

Endpoints REST protegidos para consultar produtos, estoque, movimentações.

(Opcional) Integração com ERPs, marketplaces, PowerBI, etc.

10. Upload de Anexos no Cadastro de Fornecedor

Campo(s) para upload de PDF, imagem, contrato, etc. Preview/download disponível no show/index.

Features em análise para futuro próximo

A. Requisição/Aquisição de Produto (Fluxo de Compras Internas)

Módulo para criar requisições de compra de produto (usuário solicita, gestor aprova, setor financeiro executa, estoque atualiza). Status do processo: em solicitação, aprovado, comprado, entregue.

Permitir regras customizadas de aprovação (por valor, quantidade, categoria, etc).

Permitir adicionar documentos/anexos (cotação, orçamento).

Deve ser novo menu ou módulo, integrando com movimentações e activitylog.

B. BIP de Produtos (Código de Barras/QR Code)

Associar campo código de barras/QR a cada produto.

Permitir movimentação de estoque (entrada/saída) via leitura de código (leitor USB ou câmera via browser). Sugestão de libs JS: quagga.js, html5-qrcode.

Implementar tela/modal para ler BIP, identificar produto, processar movimentação.

Gerar etiquetas com o sistema.

Eventual integração com impressora térmica para etiquetas.

Ordem de Priorização Recomendada

Auditoria/activitylog

Upload de anexos e imagens

Busca global

Permissões granulares

Relatórios exportáveis

Quick actions & atalhos

Notificações inteligentes

Timeline visual

API/integracoes externas

Upload de anexos em fornecedor

(futuro) Requisição/aquisição de produto

(futuro) BIP/código de barras em produtos

Observação Final

Este arquivo serve para ChatGPT retomar contexto instantâneo caso precise reconstruir a memória/conversa do projeto. Manter sempre atualizado conforme forem implementadas as features, e consultar README/documentação para detalhes de regras de negócio específicas do Danilo.