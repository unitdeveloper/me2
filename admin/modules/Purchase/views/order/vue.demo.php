
 
 <h3>Vue JS</h3>

<?php $Options = ['depends' => [\yii\web\JqueryAsset::className()]]; ?>
<?php $this->registerJsFile('https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js', $Options);?>

<div id="VueApp">
    <form id="search">
        Search <input name="query" v-model="search" class="form-control">
    </form>
    <table  class="table table-bordered" v-show="filteredItems.length > 0">
        <thead>
        <tr>
            <th></th>
            <th @click="sortBy('name')" class="pointer text-info">Site</th>
            <th>URL</th>
        </tr>
        </thead>
        <tbody>
            <tr is="repeat" v-for="site in filteredItems" :site="site"   ></tr>
        <tbody>
    </table>
    <div class="col-sm-12 text-right">{{ search }}</div>
</div>

<script type="text/x-template" id="tplScanRow">
    <tr>
        <td>{{ site.id }}</td>
        <td>{{ site.name }}</td>
        <td>{{ site.url }}</td>
    </tr>
</script>

<?php 
$vue =<<<JS
    
var data = [ 
            {"id":1,"name":"Google","url":"https:\/\/www.google.com.ie"},
            {"id":2,"name":"Facebook","url":"https://www.facebook.com"},
            {"id":3,"name":"Twitter ไทย","url":"https://twitter.com"}
        ];


Vue.component('repeat', {
    template: '#tplScanRow',
    props: {
        'site': Object,
    },
});

var vm = new Vue({
  
    el: '#VueApp',
    data: {
        sortKey: 'name',
        sortOrder: ['asc'],
        search: '',
        gridData:  data
    },
    computed: {
        filteredItems() {
            return this.gridData.filter(site => {
                return site.name.toLowerCase().indexOf(this.search.toLowerCase()) > -1
            })
        }
         
    },
    methods: {
        sortBy: function(key) {
            
            if (key == this.sortKey) {
                this.sortOrder = (this.sortOrder == 'asc') ? 'desc' : 'asc';
            } else {
                this.sortKey = key;
                this.sortOrder = 'asc';
            }

            console.log(this.sortOrder);
        },
        
    }
});
 
JS;
$this->registerJS($vue,\yii\web\View::POS_END);
?>