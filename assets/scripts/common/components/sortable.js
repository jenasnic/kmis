import { Sortable } from 'sortablejs';

export const bindSortable = (element) => {
  new Sortable(element, {
  });

  // If element inside form => handle submit form to update input elements with data-rank attribute
  const form = element.closest('form');
  if (form) {
    handleSubmitForm(form, element);
  }
};

const orderingSortableList = (element) => {
  const ranks = element.querySelectorAll('input[data-rank]');
  ranks.forEach((rank, index) => {
    rank.value = index.toString();
  });
};

const handleSubmitForm = (form, element) => {
  form.addEventListener('submit', () => {
    orderingSortableList(form, element);
  });
};
