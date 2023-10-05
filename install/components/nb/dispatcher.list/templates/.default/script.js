BX.ready(function (){
    BX.SidePanel.Instance.bindAnchors({
        rules: [
            {
                condition: ['/nb/disp/disp_edit.php'],
                options: sidePanelParams
            }
        ]
    });

    BX.addCustomEvent('Bitrix24.Slider:onMessage', function (event, params) {
        if(params.hasOwnProperty('dispatcherUpdate') && params.dispatcherUpdate) {
            const dispatcherGrid = BX.Main.gridManager.getInstanceById(gridId);
            if (dispatcherGrid) {
                dispatcherGrid.reload();
            }
        }
    })
});
