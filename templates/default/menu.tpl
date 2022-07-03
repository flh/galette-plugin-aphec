<h1 class="nojs">{_T string="Listes de diffusion" domain="aphec"}</h1>
<ul>
   <li><a href="{path_for name="aphec_lists_get"}">{_T string="Gérer les abonnements" domain="aphec"}</a></li>
{if $login->isAdmin()}
   <li><a href="{path_for name="aphec_lists_admin"}">{_T string="Réglages" domain="aphec"}</a></li>
{/if}
</ul>
