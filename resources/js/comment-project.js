document.addEventListener('DOMContentLoaded', () => {
  document.body.addEventListener('submit', async e => {
    if (e.target.classList.contains('comment-form')) {
      e.preventDefault();
      const form = e.target;
      const projectId = form.dataset.projectId;
      const textarea = form.querySelector('[name="comment_text"]');
      const text = textarea.value.trim();
      if (!text) return;

      const res = await fetch(`/comments/ajax-project/${projectId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ comment_text: text })
      });

      if (res.ok) {
        const html = await res.text();
        document.querySelector(`#comments-container-project-${projectId}`).insertAdjacentHTML('afterbegin', html);
        textarea.value = '';
      }
    }
  });
});
