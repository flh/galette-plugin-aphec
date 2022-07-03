{extends file="page.tpl"}
{block name="content"}
<h2>Inscription aux listes de l'APHEC</h2>
<p>Depuis cette page, vous pouvez gérer votre inscription aux différentes
listes de diffusion de l'APHEC. Pour chaque liste, vous pouvez choisir de vous
abonner ou de vous désabonner. Vous pouvez également laisser la gestion en mode
automatique : dans ce cas, les listes auxquelles vous serez inscrit seront
déterminées en fonction de la matière principale renseignée dans votre fiche
d'adhérent.</p>
<form method="POST" action="{path_for name="aphec_lists_set"}">
<ul>
{foreach from=$list_subscriptions item=subscription}
<li>
  <span class="aphec-list-title"></span>
  <span class="aphec-list-description"></span>
  <ul class="aphec-list-subscription-mode">
    <li>
      <input type="radio" id="subscription-{$subscription.list_name}-optin" name="subscription-{$subscription.list_name}" value="optin"{if $subscription.status == 'opt-in'} checked{/if}>
      <label for="subscription-{$subscription.list_name}-optin">Abonné</label>
    </li>
    <li>
      <input type="radio" id="subscription-{$subscription.list_name}-optout" name="subscription-{$subscription.list_name}" value="optout"{if $subscription.status == 'opt-out'} checked{/if}>
      <label for="subscription-{$subscription.list_name}-optout">Non abonné</label>
    </li>
    <li>
      <input type="radio" id="subscription-{$subscription.list_name}-optauto" name="subscription-{$subscription.list_name}" value="optauto"{if $subscription.status == 'automatic'} checked{/if}>
      <label for="subscription-{$subscription.list_name}-optauto">Automatique</label>
      ({if $subscription.automatic_subscribed}Abonné{else}Non abonné{/if})
    </li>
  </ul>
</li>
{/foreach}
</ul>
<p><input type="submit" name="subscription-save" value="Enregistrer les inscriptions"></p>
</form>

<h2>Gestion des options d'abonné</h2>
<p>Vous pouvez gérer la manière dont vous recevez les messages pour les listes
auxquelles vous êtes abonné. Il est notamment possible de suspendre la
réception pendant une durée donnée, ou d'opter pour une réception uniquement
d'un résumé hebdomadaire. Ces options sont accessibles en vous connectant à <a
href="https://listes.aphec.fr/wws">l'interface de gestion des listes de
diffusion</a>.
<p>Les identifiants nécessaires pour vous connecter à cette interface sont les
mêmes que pour se connecter à la gestion de votre fiche d'adhérent.
{/block}
