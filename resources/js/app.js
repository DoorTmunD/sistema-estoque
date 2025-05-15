import './bootstrap';
import Cleave from 'cleave.js';
import NProgress from 'nprogress';
import 'nprogress/nprogress.css';

window.Cleave = Cleave;

function initCleaveMasks() {
  // Máscara monetária para Preço Unitário
  document.querySelectorAll('#unit_price').forEach((el) => {
    new Cleave(el, {
      numeral: true,
      numeralDecimalMark: ',',
      delimiter: '.',
      prefix: 'R$ ',
      numeralDecimalScale: 2
    });

    // quando ganhar foco, posiciona o cursor no final para facilitar a digitação
    el.addEventListener('focus', () => {
      setTimeout(() => {
        const len = el.value.length;
        el.setSelectionRange(len, len);
      }, 0);
    });
  });

  // Máscara numérica para Quantidade Inicial (sem casas decimais)
  document.querySelectorAll('#initial_quantity').forEach((el) => {
    new Cleave(el, {
      numeral: true,
      numeralDecimalMark: ',',
      delimiter: '.',
      numeralDecimalScale: 0
    });

    el.addEventListener('focus', () => {
      setTimeout(() => {
        const len = el.value.length;
        el.setSelectionRange(len, len);
      }, 0);
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  initCleaveMasks();
  NProgress.configure({ showSpinner: false });

  // Barra de progresso para links normais
  document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      NProgress.start();
    });
  });

  window.addEventListener('load', () => {
    NProgress.done();
  });
});

document.addEventListener('livewire:load', () => {
  initCleaveMasks();
  if (window.Livewire) {
    window.Livewire.hook('message.sent', () => NProgress.start());
    window.Livewire.hook('message.processed', () => {
      initCleaveMasks();
      NProgress.done();
    });
  }
});