{extends file="page.tpl"}
{block name="content"}
<h2>Réglages des listes de l'APHEC</h2>
<p>Vous pouvez choisir quelles listes sont accessibles aux adhérents en
inscription libre. De plus, pour chacune de ces listes, vous pouvez définir des
réglages par défaut, de telle sorte qu'en fonction de leur matière les
adhérents sont automatiquement inscrits. Ils peuvent manuellement, depuis leur
compte, faire ensuite d'autres choix que ce réglage par défaut.</p>
<form method="POST" action="{path_for name="aphec_lists_admin_set"}">
<ul>
{foreach $aphec_lists as $list_id => $aphec_list}
<li>
  <span class="aphec-list-title">{$aphec_list.name}</span>
  <span class="aphec-list-description">{$aphec_list.description}</span>
  <span class="aphec-list-authorized">
    <input type="checkbox" id="list-{$list_id}-authorized" name="list-{$list_id}"{if $aphec_list.authorized} checked{/if}>
    <label for="list-{$list_id}-authorized">Accessible aux adhérents</label>
  </span>
  <span class="aphec-list-profiles">
    <label for="list-{$list_id}-profiles">Profils inscrits par défaut</label>
    <select name="list-{$list_id}-profiles[]" multiple>
      {foreach $matieres as $matiere_id => $matiere_label}
        <option value="{$matiere_id}"{if $aphec_list.matieres.$matiere_id} selected{/if}>{$matiere_label}</option>
      {/foreach}
    </select>
  </span>
</li>
{/foreach}
</ul>
<p><input type="submit" name="list-profiles-save" value="Enregistrer les inscriptions"></p>
</form>
