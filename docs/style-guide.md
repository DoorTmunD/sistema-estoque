# Guia de Estilo UI/UX

## Paleta de Cores
- Tons neutros escuros (#1F2937) e claros (#F9FAFB).
- Destaques em azul suave (#3B82F6) e verde (#10B981).

## Tipografia
- Fonte principal: Inter (via Google Fonts).
- Hierarquia: `text-xl` para títulos, `text-base` para corpo.

## Componentes
- **Botões**: cantos arredondados (`rounded-lg`), sombra suave (`shadow-sm`), padding `px-4 py-2`.
- **Cards**: fundo branco, borda leve, `p-6` e `rounded-2xl`.
- **Formulários**: inputs com `focus:ring-2 focus:ring-blue-400`.

## Responsividade
- Mobile-first: grid único em `<md`; dois colunas em `md:grid-cols-2`.
- Navbar fixa no topo em todos tamanhos.

## Boas práticas
- Separar lógica do layout via Livewire Components.
- Reutilizar componentes com Blade e `@include`.
- Seguir padrão BEM ou Tailwind como preferencial.
