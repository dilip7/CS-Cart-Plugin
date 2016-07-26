{* $Id$ *}

<div class="form-field">
    <label for="quikwallet_url">{__("quikwallet_url")}:</label>
    <input type="text" name="payment_data[processor_params][quikwallet_url]" id="quikwallet_url" value="{$processor_params.quikwallet_url}" class="input-text" />
</div>

<div class="form-field">
    <label for="quikwallet_partnerid">{__("quikwallet_partnerid")}:</label>
    <input type="text" name="payment_data[processor_params][quikwallet_partnerid]" id="quikwallet_partnerid" value="{$processor_params.quikwallet_partnerid}" class="input-text" />
</div>

<div class="form-field">
    <label for="quikwallet_secret">{__("quikwallet_secret")}:</label>
    <input type="text" name="payment_data[processor_params][quikwallet_secret]" id="quikwallet_secret" value="{$processor_params.quikwallet_secret}" class="input-text" />
</div>

<div class="form-field">
    <label for="currency">{__("currency")}:</label>
    <select name="payment_data[processor_params][currency]" id="currency">
        <option value="INR" {if $processor_params.currency == "INR"}selected="selected"{/if}>{__("currency_code_inr")}</option>
    </select>
</div>

<div class="form-field">
    <label for="iframe_mode_{$payment_id}">{__("iframe_mode")}:</label>
    <select name="payment_data[processor_params][iframe_mode]" id="iframe_mode_{$payment_id}">
        <option value="Y" selected="selected">{__("enabled")}</option>
        <option value="N" >{__("disabled")}</option>
    </select>
</div>
