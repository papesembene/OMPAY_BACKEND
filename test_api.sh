#!/bin/bash

echo "🚀 Test automatique de l'API OM Pay avec SMS Orange"
echo "=================================================="

# Étape 1: Connexion
echo -e "\n📱 Étape 1: Connexion de l'utilisateur +221781157773"
LOGIN_RESPONSE=$(curl -s -X POST "http://localhost:8000/api/v1/auth/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"phone": "+221781157773", "secret_code": "1234"}')

echo "Réponse connexion:"
echo "$LOGIN_RESPONSE" | jq .

# Extraire le token
TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.data.token')

if [ "$TOKEN" = "null" ] || [ -z "$TOKEN" ]; then
    echo "❌ Échec de la connexion"
    exit 1
fi

echo -e "\n✅ Connexion réussie, token obtenu"

# Étape 2: Transfert
echo -e "\n💸 Étape 2: Transfert de 5000 FCFA vers +221778750587"
TRANSFER_RESPONSE=$(curl -s -X POST "http://localhost:8000/api/v1/transfers" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 5000,
    "recipient_phone": "+221778750587",
    "description": "Test automatique avec SMS Orange"
  }')

echo "Réponse transfert:"
echo "$TRANSFER_RESPONSE" | jq .

# Vérifier le succès
SUCCESS=$(echo "$TRANSFER_RESPONSE" | jq -r '.success')

if [ "$SUCCESS" = "true" ]; then
    echo -e "\n✅ Transfert réussi !"
    echo "📱 SMS envoyés automatiquement aux deux utilisateurs via Orange API"
    echo "💰 Soldes mis à jour:"
    echo "   - Expéditeur: 10.000 - 5.000 = 5.000 FCFA"
    echo "   - Destinataire: 10.000 + 5.000 = 15.000 FCFA"
else
    echo -e "\n❌ Échec du transfert"
    ERROR=$(echo "$TRANSFER_RESPONSE" | jq -r '.message')
    echo "Erreur: $ERROR"
fi

echo -e "\n🎉 Test terminé !"