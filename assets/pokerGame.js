export default class CardClient {
    static attachCardListeners() {
        if (! document.getElementById('game-cards')) {
            return;
        }
    
        const cards = document.getElementById('game-cards').children;
    
        for (const card of cards) {
            if (card.dataset.type !== 'game-card') {
                continue;
            }

            card.addEventListener('click', function() {
                if (this.classList.contains('selected')) {
                    this.classList.remove('selected');
                } else {
                    this.classList.add('selected');
                }
            });
        }
    }
    
    static attachCardSubmitListener() {
        if (! document.getElementById('cards-submit')) {
            return;
        }
    
        document.getElementById('cards-submit').addEventListener('click', CardClient.submitCards);
    }
    
    static async submitCards() {
        if (! document.getElementById('game-cards')) {
            return;
        }
    
        const cards = document.getElementById('game-cards').children;
        const cardIndices = [];
    
        for (const [index, card] of Object.entries(cards)) {
            if (card.classList.contains('selected')) {
                cardIndices.push(parseInt(index));
            }
        }
        const newCardHand = await CardClient.sendRequest('proj/game/api/changeCards', cardIndices);
        
        CardClient.updatePlayingFieldCards(newCardHand);
    }

    static updatePlayingFieldCards(newCardHand) {
        if (! document.getElementById('game-cards')) {
            return;
        }

        const currentPlayingFieldCards = document.getElementById('game-cards').children;
        for (const [index, card] of Object.entries(currentPlayingFieldCards)) {
            if (card.dataset.type !== 'game-card') {
                continue;
            }
    
            card.innerHTML = newCardHand[index].utf8;
        }

        CardClient.deselectAllCards();
    }

    static deselectAllCards() {
        if (! document.getElementById('game-cards')) {
            return;
        }

        const cards = document.getElementById('game-cards').children;
        for (const card of cards) {
            if (card.dataset.type !== 'game-card') {
                continue;
            }

            card.classList.remove('selected');
        }
    }
    
    static async sendRequest(endpoint, data) {
        const url = `http://localhost:8888/${endpoint}`;
        const response = await fetch(url, {
            method: 'POST',
            body: JSON.stringify(data)
        });
        const responseData = await response.json();

        return responseData;
    }
}
    