<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{block name='title'}Blog Smarty{/block}</title>
  <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
  <a class="skip-link" href="#main-content">Skip to content</a>

  <header class="site-header">
    <div class="container site-header__inner">
      <a class="site-logo" href="/">Blog Smarty</a>

      <nav class="site-nav" aria-label="Main navigation">
        <a class="site-nav__link" href="/">Home</a>
      </nav>
    </div>
  </header>

  <main id="main-content">
    {block name='content'}{/block}
  </main>

  <footer class="site-footer">
    <div class="container site-footer__inner">
      <a class="site-logo" href="/">Blog Smarty</a>
      <p class="site-footer__copyright">&copy; 2026 Blog Smarty</p>
    </div>
  </footer>
</body>
</html>
