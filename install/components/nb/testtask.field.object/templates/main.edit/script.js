BX.ObjectsTagSelector = function (params) {
  this.inputContainer = document.getElementById('tag-selector-result__' + params.fieldFormName);
  this.fieldName = params.fieldName;
  this.userField = params.userFieldName;

  let entityControl = false;

  if (BX.UI && BX.UI.EntityEditor) {
    const entityEditor = BX.UI.EntityEditor.getDefault();
    entityControl = entityEditor.getControlById(params.fieldName);
  }

  const fieldInput = document.getElementById(params.fieldName + '_input');

  this.tagSelector = new BX.UI.EntitySelector.TagSelector({
    multiple: params.multiple,
    events: {
      onAfterTagAdd: function (event) {
        const {tag} = event.getData(),
          tagId = tag.getId();
        if (params.multiple) {
          const inputValue = fieldInput.value.length ? fieldInput.value.split(',') : [],
            valueIndex = inputValue.indexOf(tagId);
          if (valueIndex === -1) {
            fieldInput.value += fieldInput.value.length ? `,${tagId}` : `${tagId}`;
          }
        } else {
          const inputValue = fieldInput.value.length ? fieldInput.value.split(',') : [];
          if (!inputValue.includes(tagId))
            inputValue.push(tagId);
          fieldInput.value = inputValue.join(',');
        }

        entityControl && entityControl.markAsChanged();

      }.bind(this),
      onAfterTagRemove: function (event) {
        const {tag} = event.getData(),
          tagId = tag.getId();
        if (!params.multiple) {
          BX.remove(this.inputContainer.querySelector('input[type="hidden"][name="' + params.fieldName + '"]'))
        } else {
          const inputValue = fieldInput.value.length ? fieldInput.value.split(',') : [],
            valueIndex = inputValue.indexOf(tagId);
          if (valueIndex !== '-1') {
            inputValue.splice(valueIndex, 1);
            fieldInput.value = inputValue.join(',');
          }
        }
        entityControl && entityControl.markAsChanged();
      }.bind(this)
    },
    dialogOptions: {
      dropdownMode: true,
      preload: true,
      context: 'objects',
      entities: [{
        id: 'objects',
        options: {
          selectedItemIds: params.selectedItemIds,
        },
        dynamicLoad: true,
        dynamicSearch: true,
      }],
      tabs: params.tabs,
      selectedItems: params.selectedItems
    }
  });
  this.tagSelector.renderTo(document.getElementById('tag-selector__' + params.fieldFormName));
}
