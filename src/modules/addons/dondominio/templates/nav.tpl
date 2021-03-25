<nav class="navbar navbar-default">
  <div class="collapse navbar-collapse" >
    <ul class="nav navbar-nav">
      {foreach from=$nav item=navbar}
        {if $navbar.selected eq true}
          <li><a class="navbar-selected" href="{$navbar.link}">{$navbar.title}</a></li>
        {else}
          <li><a href="{$navbar.link}">{$navbar.title}</a></li>
        {/if}
      {/foreach}
    </ul>
  </div>
</nav>