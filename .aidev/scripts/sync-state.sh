#!/bin/bash

# aid-sync-state.sh
# Sincroniza o progresso do ROADMAP.md com os arquivos de estado do aidev

ROADMAP=".aidev/plans/ROADMAP.md"
SESSION=".aidev/state/session.json"
UNIFIED=".aidev/state/unified.json"

echo "▸ Sincronizando estado do AI Dev..."

# Identifica o último Sprint concluído
LAST_COMPLETED_SPRINT=$(grep -B 5 "✅ Concluído" "$ROADMAP" | grep "SPRINT" | tail -n 1 | sed -E 's/.*SPRINT ([0-9]+).*/\1/')
NEXT_SPRINT_CANDIDATE=$(grep -B 5 "🔵 Planejado" "$ROADMAP" | grep "SPRINT" | head -n 1 | sed -E 's/.*SPRINT ([0-9]+).*/\1/')

CURRENT_SPRINT=${NEXT_SPRINT_CANDIDATE:-$((LAST_COMPLETED_SPRINT + 1))}

echo "  Sprint Detectado: $CURRENT_SPRINT (Último concluído: $LAST_COMPLETED_SPRINT)"

# Atualiza session.json (simplificado para demonstração, idealmente usaria jq)
if command -v jq >/dev/null 2>&1; then
    jq --arg sprint "$CURRENT_SPRINT" '.current_sprint = ($sprint | tonumber)' "$SESSION" > "$SESSION.tmp" && mv "$SESSION.tmp" "$SESSION"
    jq --arg progress "Sprint $LAST_COMPLETED_SPRINT complete, working on $CURRENT_SPRINT" '.sprint_progress = $progress' "$UNIFIED" > "$UNIFIED.tmp" && mv "$UNIFIED.tmp" "$UNIFIED"
    echo "  ✓ JSONs atualizados com jq."
else
    # Fallback básico com sed se jq não estiver disponível
    sed -i "s/\"current_sprint\": [0-9]*/\"current_sprint\": $CURRENT_SPRINT/" "$SESSION"
    echo "  ! jq não encontrado, usando sed (atualização limitada)."
fi

echo "✓ Sincronização concluída."
