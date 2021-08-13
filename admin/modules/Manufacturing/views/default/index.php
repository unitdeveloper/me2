<div class="Manufacturing-default-index" ng-app="myApp">
<div class="controller" >
    <input type="text" class="form-control" ng-model="nameX">


</div>
    <h1><?= $this->context->action->uniqueId ?></h1>
    <p ng-init="param=[
    {id:1,name:'Than Assawin',salary:50000},
    {id:2,name:'Ton Weerapong',salary:30000},
    {id:3,name:'Jaw Jinyee',salary:80000}
    ]">
        This is the view content for action "<?= $this->context->action->id ?>".
        The action belongs to the controller "<?= get_class($this->context) ?>"
        in the "<?= $this->context->module->id ?>" module.
    </p>
    <p>
        You may customize this page by editing the following file:<br>
        <code><?= __FILE__ ?></code>
    </p>

    <input type="text" class="form-control" ng-model="queryString.name">
    <input type="radio" value="name" ng-model="sortString" id="sort-name" > <label for="sort-name"> Sort Name</label>
    <input type="radio" value="-name" ng-model="sortString" id="sort-name2" > <label for="sort-name2"> Sort Name (Desc)</label>
    <table class="table" >
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Salary</th>
            </tr>
        </thead>
        <tr ng-repeat="n in param | filter:queryString | orderBy:sortString">
            <td>{{ n.id }}</td>
            <td> {{ n.name }}</td>
            <td> {{ n.salary | currency:' '}}</td>
        </tr>

    </table>
     ss
</div>

<script>
var myApp = angular.module("myApp", []);
 
     
    function homeController()
    {
        console.log('test');
    }


    
 
    
</script>
