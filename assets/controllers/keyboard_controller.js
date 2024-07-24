import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['key', 'guessInput', 'form'];
    static values = {
      wordLength: Number,
      gameId: Number,
    }

    connect() {
        document.addEventListener('keydown', (event) => this.handleKeydown(event));
    }

    handleKeydown(event) {
      if (event.key === 'Backspace') {
        this.deleteGuesses();
      } else if (event.key === 'Enter') {
        this.submitGuess();
      } else if (event.key.length === 1 && event.key.match(/[a-z]/i)) {
            this.populateGuess(event.key.toUpperCase());
        }
    }

    handleButtonClick(event) {
      const letter = event.target.dataset.letter;
      console.log(letter);
      if (letter === 'Backspace') {
        this.deleteGuesses();
      } else if (letter === 'Enter') {
        this.submitGuess();
      } else {
        if (letter) {
          this.populateGuess(letter.toUpperCase());
        }
      }
    }

    populateGuess(letter) {
      // Check if the current number of guesses is equal to or greater than this.wordLengthValue
      const currentGuessCount = this.guessInputTargets.filter(input => input.value !== '').length;
      if (currentGuessCount >= this.wordLengthValue) {
        // Do not populate the guess if the condition is met
        return;
      }

      // Find the next empty guess slot and populate it
      let emptySlot = this.guessInputTargets.find(input => input.value === '');
      if (emptySlot) {
        emptySlot.value = letter;
      }
    }

    updateKeyboard(letter, evaluation) {
        // Find the key element and update its class based on the evaluation
        let keyElement = this.keyTargets.find(key => key.dataset.letter === letter);
        if (keyElement) {
            keyElement.classList.add(evaluation); // 'absent', 'present', 'correct'
        }
    }

    deleteGuesses() {
      // Find the rightmost non-empty guess input
      const nonEmptyInputs = this.guessInputTargets.filter(input => input.value !== '');
      const lastNonEmptyInput = nonEmptyInputs.pop();
      if (lastNonEmptyInput) {
        lastNonEmptyInput.value = ''; // Clear the value
      }
    }

    submitGuess() {
      // find the form and submit it
     console.log(this.formTarget);
     this.formTarget.submit();
     // this.formTarget().submit();
      // Submit the form
      // const guessData = this.guessInputTargets.map(input => input.value).join('');
      // const url = '/guess'; // Adjust the URL to your route
      // fetch(url, {
      //   method: 'POST',
      //   headers: {
      //     'Content-Type': 'application/json',
      //     'Accept': 'text/vnd.turbo-stream.html',
      //   },
      //   body: JSON.stringify({ guess: guessData, gameId: this.gameIdValue }),
      // })
      //   .then(response => response.text())
      //   .then(html => {
      //     Turbo.Stream.renderStreamMessage(html);
      //   });
    }
}
