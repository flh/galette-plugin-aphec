{extends file="page.tpl"}
{block name="content"}
<h2>Réglages des listes de l'APHEC</h2>
<p>Vous pouvez choisir quelles listes sont accessibles aux adhérents en
inscription libre. De plus, pour chacune de ces listes, vous pouvez définir des
réglages par défaut, de telle sorte qu'en fonction de leur matière les
adhérents sont automatiquement inscrits. Ils peuvent manuellement, depuis leur
compte, faire ensuite d'autres choix que ce réglage par défaut.</p>
<form method="POST" action="{path_for name="aphec_lists_admin_set"}">
<ul id="aphec-lists-admin">
{foreach $aphec_lists as $list_id => $aphec_list}
<li class="aphec-list">
  <span class="aphec-list-title ui-state-active ui-corner-top">{$aphec_list.name}</span>
  <span class="aphec-list-description">{$aphec_list.description}</span>
  <span class="aphec-list-authorized">
    <input type="checkbox" id="list-{$list_id}-authorized" name="list-{$list_id}-authorized"{if $aphec_list.authorized} checked{/if}>
    <label for="list-{$list_id}-authorized">Accessible aux adhérents</label>
  </span>
  <span class="aphec-list-profiles">
    <label for="list-{$list_id}-profiles[]">Profils inscrits par défaut</label>
    <ul>
      {foreach $matieres as $matiere_id => $matiere_label}
      <li><label><input type="checkbox" name="list-{$list_id}-profiles[]" value="{$matiere_id}"{if $matiere_id|in_array:$aphec_list.matieres} checked{/if}>{$matiere_label}</label></li>
      {/foreach}
    </ul>
  </span>
</li>
{/foreach}
</ul>
<p><input type="submit" name="list-profiles-save" value="Enregistrer les réglages">
{include file="forms_types/csrf.tpl"}</p>
</form>
{/block}
