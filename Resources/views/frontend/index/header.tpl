{extends file='parent:frontend/index/header.tpl'}

{block name="frontend_index_header_favicons"}
    {$smarty.block.parent}

    {foreach $bucoFontPreload as $font}
        <link crossorigin="anonymous" rel="preload" as="font" type="{$font.type}" href="{include file="string:{$font.url}"}">
    {/foreach}
{/block}