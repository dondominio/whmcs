<ul class="dd-breadcrumb">
	{foreach from=$breadcrumbs item=bread}
	<li>
		<a href="{$bread.link}">{$bread.title}</a>
		<span class="fa fa-angle-right"></span>
	</li>
	{/foreach}
</ul>
