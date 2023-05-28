const baseUrl = getBaseUrl();
function getBaseUrl() {
    /** 
     * Dumb solution but i dont have time to fiddle
     * with ENVS on a server i dont control  
     */
    const baseUrl = window.location.href.includes('localhost') ? 'http://localhost:8888' :
        'http://www.student.bth.se/~lifr21/dbwebb-kurser/mvc/me/report/public/';
    
    const cleanedBaseUrl = baseUrl.replace(/\/$/, '');
    return cleanedBaseUrl
}
export default class CardClient {
    gameCardContainer

    constructor() {
        this.gameCardContainer = document.getElementById('game-cards');
        this.attachCardListeners();
        this.attachCardSubmitListener();
        this.getPreviousAction();
    }

    async attachCardListeners() {
        if (!this.gameCardContainer) {
            return;
        }

        const currentRound = await this.sendRequest('proj/game/api/current-round');
        if (currentRound.data !== 2) {
            return;
        }

        for (const card of this.gameCardContainer.children) {
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

    async getPreviousAction() {
        const lastActionElem = document.getElementById('last-action');
        if (!lastActionElem) {
            return;
        }

        const response = await this.sendRequest('proj/game/api/previous-action');
        if (response.status !== 200) {
            return;
        }

        lastActionElem.innerHTML = response.data;

        return response.data;
    }
    
    attachCardSubmitListener() {
        if (!this.gameCardContainer || !document.getElementById('cards-submit')) {
            return;
        }
    
        document.getElementById('cards-submit').addEventListener('click', this.submitCards.bind(this));
    }
    
    async submitCards() {
        if (!this.gameCardContainer) {
            return;
        }

        const cardIndices = [];
        for (const [index, card] of Object.entries(this.gameCardContainer.children)) {
            if (card.classList.contains('selected')) {
                cardIndices.push(parseInt(index));
            }
        }
        const newCardHand = await this.sendRequest('proj/game/api/changeCards', cardIndices, 'POST');

        if (newCardHand.status !== 200) {
            alert(newCardHand.data);
            return;
        }
        this.updatePlayingFieldCards(newCardHand.data);
    }

    updatePlayingFieldCards(newCardHand) {
        if (! this.gameCardContainer) {
            return;
        }

        for (const [index, card] of Object.entries(this.gameCardContainer.children)) {
            if (card.dataset.type !== 'game-card') {
                continue;
            }
    
            card.innerHTML = newCardHand[index].utf8 ? newCardHand[index].utf8 : card.innerHTML;
        }

        this.deselectAllCards();

        /** Redirect the player when card change has been made */
        window.location.replace(`${baseUrl}/proj/game/done-change`)
    }

    deselectAllCards() {
        if (! this.gameCardContainer) {
            return;
        }

        for (const card of this.gameCardContainer.children) {
            if (card.dataset.type !== 'game-card') {
                continue;
            }

            card.classList.remove('selected');
        }
    }
    
    async sendRequest(endpoint, data, method = 'GET') {
        const responseData = {};
        const url = `${baseUrl}/${endpoint}`;

        let response;

        if (method === 'GET') {
            response = await fetch(url)
        } else {
            response = await fetch(url, {
                method: method,
                body: JSON.stringify(data)
            });
        }
        responseData.status = response.status;
        responseData.data = await response.json();

        return responseData;
    }
}
    