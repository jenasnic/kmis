const initializePreviewButton = (button) => {
  button.addEventListener('click', (event) => {
    event.preventDefault();
    fetch(button.dataset.previewUrl, {
      credentials: 'same-origin',
      method: 'GET',
    })
        .then(response => response.text())
        .then((html) => {
          const modal = document.getElementById('modal-payment-preview');
          const modalContent = document.getElementById('modal-payment-content');
          modalContent.innerHTML = html;
          modal.classList.add('is-active');
        })
    ;
  });
}

const initializeModalPreview = () => {
  const modal = document.getElementById('modal-payment-preview');
  modal.querySelectorAll('.modal-close, .modal-background').forEach((element) => {
    element.addEventListener('click', () => {
      modal.classList.remove('is-active');
    });
  })
};

const paymentListTable = document.getElementById('payment-list-table');

if (paymentListTable) {
  paymentListTable.querySelectorAll('button[data-preview-url]').forEach(initializePreviewButton)
  initializeModalPreview();
}
