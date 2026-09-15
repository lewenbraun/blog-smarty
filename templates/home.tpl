{extends file='layouts/base.tpl'}

{block name='content'}
  <h1 class="visually-hidden">Blog Smarty</h1>

  {foreach $categoryArticles as $categorySection}
    <section
      class="category-section"
      aria-labelledby="category-{$categorySection->category->getSlug()}"
    >
      <div class="container">
        <header class="category-section__header">
          <h2
            class="category-section__title"
            id="category-{$categorySection->category->getSlug()}"
          >
            {$categorySection->category->getName()}
          </h2>

          <a
            class="category-section__all-link"
            href="/categories/{$categorySection->category->getSlug()}"
          >
            All articles
          </a>
        </header>

        <div class="article-grid">
          {foreach $categorySection->articles as $article}
            {include file='components/article-card.tpl' article=$article}
          {/foreach}
        </div>
      </div>
    </section>
  {foreachelse}
    <div class="container">
      <p class="home__empty">No articles yet.</p>
    </div>
  {/foreach}
{/block}
