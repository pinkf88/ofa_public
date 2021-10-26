<div id="control_tab_analytics">
<h4 class="control">ANALYTICS</h4>
<p class="control_admin">
    <a href="javascript:control_admin('update_analytics');"><span class="ui-icon ui-icon-arrowrefresh-1-e"></span></a>&nbsp;&nbsp;&nbsp;
    <a href="javascript:control_admin('update_analytics');">Update Analytics</a>
</p>
<p class="control_admin">
    <div class="control_admin_overview">
    <a href="javascript:control_admin('update_analytics');"><span class="ui-icon ui-icon-arrowrefresh-1-e"></span></a>&nbsp;&nbsp;&nbsp;
    <a href="javascript:control_analyticsOverview();"><span id="analytics_overview_title">Übersicht</span></a>
    </div>
    <div class="control_admin_overview">
        <select name="control_admin_overview_days" id="control_admin_overview_days" class="control_admin_overview_days">
            <option value="1">1 Tag</option>
            <option value="2">2 Tage</option>
            <option value="3">3 Tage</option>
            <option value="5">5 Tage</option>
            <option value="10" selected="selected">10 Tage</option>
            <option value="30">30 Tage</option>
            <option value="60">60 Tage</option>
            <option value="90">90 Tage</option>
        </select>
    </div>
    <div style="clear: both;"></div>
</p>
<div id="analytics_overview" class="analytics_overview"></div>
<div id="analytics_auswahl" class="analytics_auswahl"></div>
</div>
