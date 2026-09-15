{extends file='layouts/base.tpl'}

{block name='title'}Page not found — Blog Smarty{/block}

{block name='content'}
  <section class="error-page container container--narrow">
    <p class="error-page__code">404</p>
    <h1 class="error-page__title">Page not found</h1>
    <p class="error-page__description">The page may have been moved or no longer exists.</p>
    <a class="text-link" href="/">Return home</a>
  </section>
{/block}
