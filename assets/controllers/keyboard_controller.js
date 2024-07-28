import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {
    static targets = ['key', 'guessInput'];
    static values = {
      wordLength: Number,
      gameId: Number,
    }

    async initialize() {
      this.component = await getComponent(this.element);
    }

    handleKeyDown(event) {
      this.component.emitSelf('keyDown', { key: event.key });
    }
}
