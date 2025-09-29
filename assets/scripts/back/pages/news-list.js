const initializePreviewButton = (button) => {
  button.addEventListener('click', (event) => {
    event.preventDefault();
    const modal = document.getElementById('modal-news-preview');
    const iframe = document.getElementById('iframe-preview');
    iframe.src = button.dataset.previewUrl;
    modal.classList.add('is-active');
  });
}

const initializeModalPreview = () => {
  const modal = document.getElementById('modal-news-preview');
  modal.querySelectorAll('.modal-close, .modal-background').forEach((element) => {
    element.addEventListener('click', () => {
      modal.classList.remove('is-active');
    });
  })
};

const newsListForm = document.getElementById('news-list-form');

if (newsListForm) {
  newsListForm.querySelectorAll('button[data-preview-url]').forEach(initializePreviewButton)
  initializeModalPreview();
}
