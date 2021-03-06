{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
	<h3><i class="icon icon-credit-card"></i> {l s='Formation' mod='formation'}</h3>
	<p>
		<strong>{$formation_description}</strong><br />
	</p>
</div>

<div class="panel">
	<h3><i class="icon icon-tags"></i> {l s='Documentation' mod='formation'}</h3>
	<p>
		&raquo; {l s='Access to the automatic generation of a PrestaShop module' mod='formation'} :
		<ul>
			<li><a href="https://validator.prestashop.com/generator" target="_blank">{l s='Module generator' mod='formation'}</a></li>
			<li><a href="https://devdocs.prestashop.com/1.7/modules/" target="_blank">{l s='Module documentation' mod='formation'}</a></li>
		</ul>
	</p>
</div>
<div class="panel">
	<span id="product_sync_action" class="button btn btn-default ">
		<span>{l s='Launch Listing Sync' mod='formation'}</span>
	  </span>
</div>
<script>
 $('#product_sync_action')
	.removeAttr('disabled') // Remove disabled attribute
	.click (function(){
		// Ajax call with secure token
		//$.get('../modules/reverb/cron.php?code=products',
		$.post('{$ajax_url}&action=Foo&ajax=true',
				function (response) {
					console.log('ok pour ajax.');
				}
		)
				.fail(function() {
					console.log('ko pour ajax.');
				});
	});
</script>
