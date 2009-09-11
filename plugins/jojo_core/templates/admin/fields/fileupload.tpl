<span title="{$filesize}"><a href="{_SITEURL}/downloads/{$fd_table}s/{value}" target="_BLANK">
<img src="{$filelogo}" border="0" align="absmiddle"> {$value} </a></span><br>

<span title="Actual size {$imagesize[0]}x{$imagesize[1]}px {$filesize}">
    <img src="images/{$this->viewthumbsize}{$this->fd_table}s/{$this->value}" border="0" align="absmiddle" alt="{$this->value}"/>
</span><br />

<input type="hidden" name="fm_{$this->fd_field}" value="{$this->value}" />
<div style="color: #999">{$this->value}</div>
<input type="hidden" name="MAX_FILE_SIZE" value="{$this->fd_maxvalue}" />
<input {$class} type="file" name="fm_FILE_{$this->fd_field}" id="fm_FILE_{$this->fd_field}"  size="{$this->fd_size}" value="" {$readonly} onchange="fullsave = true;" title="{fd_help}" />