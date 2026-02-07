# Laravel & TALL Stack Style Guide

## PHP & Laravel
- Seguir as PSRs e o padrão Laravel (Pint).
- Usar Constructor Property Promotion.
- Tipagem estrita em retornos e parâmetros.

## TALL Stack (Tailwind, Alpine, Laravel, Livewire)
- **Livewire:** Priorizar componentes enxutos, delegando lógica pesada para Services.
- **Alpine.js:** Usar para estados puramente de UI (ex: modais, dropdowns).
- **Tailwind:** Seguir a ordem de classes padrão e evitar redundâncias.

## IA & MCP Integration
- **Laravel Boost:** Sempre consultar o schema do banco e comandos Artisan via MCP antes de propor mudanças.
- **Context7:** Consultar documentação oficial para evitar códigos obsoletos.
- **Serena & Basic Memory:** Utilizar para análise semântica e busca de contexto persistente, visando economia de tokens.
- **Orquestração (aidev):** O Conductor atua na gestão de trilhas (tracks) e planos, enquanto o aidev orquestra a execução assistida.
