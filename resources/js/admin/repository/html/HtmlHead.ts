/**
 * csrfトークンを返す
 */
function csrfToken(): string {
  const token = document.head.querySelector('meta[name="csrf-token"]');
  if (!(token instanceof HTMLMetaElement)) {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
  }
  return token instanceof HTMLMetaElement ? token.content : '';
}

/**
 * meta タグの中身
 * @param name
 */
function getMetaContent(name: string): string {
  const meta = document.head.querySelector(`meta[name="${name}"]`);
  return meta instanceof HTMLMetaElement ? meta.content : '';
}
export { csrfToken, getMetaContent };
