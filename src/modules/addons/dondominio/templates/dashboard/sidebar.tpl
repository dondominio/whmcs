<div class="sidebar-header">
    <img src="https://www.dondominio.com/images/favicon_appletouch.png" class="absmiddle" width="16" height="16" /> DonDominio Manager
</div>

<ul class="menu">

    {foreach from=$sidebar item=section}

    <li {if $section.selected} class="selected" {/if} ><a href="{$section.link}">{$section.title}</a></li>
    {/foreach}

</ul>