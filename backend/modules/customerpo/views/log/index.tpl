{use class="kartik\grid\GridView"}
{use class="kartik\export\ExportMenu"}
{title}Logs{/title}

{if $gridViewDataProvider}
    {GridView::widget([
        'dataProvider' => $gridViewDataProvider,
        'showFooter' => TRUE,
        'hover' => TRUE,
        'responsiveWrap' => FALSE,
        'columns' => $columns,
        'floatHeader' => TRUE,
        'floatHeaderOptions' => ['position' => 'absolute']
    ])}
{/if}