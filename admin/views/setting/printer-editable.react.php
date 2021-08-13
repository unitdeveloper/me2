<?php admin\assets\ReactAsset::register($this); ?>

<?php $Options = ['depends' => [admin\assets\ReactAsset::className()]]; ?> 
<?php $this->registerJsFile('//npmcdn.com/react-bootstrap-table/dist/react-bootstrap-table.min.js',$Options) ?>
<?php $this->registerCssFile('//npmcdn.com/react-bootstrap-table/dist/react-bootstrap-table-all.min.css', $Options); ?>
<!-- //http://allenfang.github.io/react-bootstrap-table/example.html#basic  -->


<script type="text/babel">
/**
 * @license Ewinl
 * v3.06.05 - 2018
 * react.development.js
 * 
 * Copyright (c) 2017-present, Ewinl, Co,Ltd.
 * 
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 * 
 */


var dataTable = [];

$.ajax({
    url:"index.php?r=setting/print-manual",
    type:'GET',
    dataType:'JSON',
    async:false,
    success:function(response){
        dataTable.push(response.data);
    }
})

ReactDOM.render(
  <span>Variables</span>,
  document.getElementById('tableTitle')
);


class EditTable extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            data : this.props.dataArray
        }
    }   

    render() {


        const onBeforeSaveCell = (row, cellName, cellValue) => {
            // You can do any validation on here for editing value,
            // return false for reject the editing
            //console.log(`${row.id} => Before cell ${cellName} with value ${cellValue}`);
            return true;
        }

        const onAfterSaveCell = (row, cellName, cellValue) => {
 
            //console.log(`${row.id} => After Save cell ${cellName} with value ${cellValue}`);
            const self  = this
            $.ajax({
                url:"index.php?r=setting/print-manual-update",
                type:'POST',
                data:{id:row.id,field:cellName,data:cellValue},
                dataType:'JSON',
                async:false,
                success:function(response){
                    self.setState({
                        data : self.state.data
                    }) 
                    load()
                }
            })

            // let rowStr = '';
            // for (const prop in row) {
            //     rowStr += prop + ': ' + row[prop] + '\n';
            // }

            // console.log('Thw whole row :\n' + rowStr);
        }

        const buttonRemove = (cell, row) => {
            const self  = this
            function handleRemove(e,data){
            e.preventDefault();
            
            if(confirm('Remove "'+data.variable+'"?')){   


                    $.ajax({
                        url:"index.php?r=setting/print-manual-remove",
                        type:'POST',
                        data:{id:data.id},
                        dataType:'JSON',
                        async:true,
                        success:function(response){    
                            
                            if(response.status==200){
                                var index = self.state.data.indexOf(data);
                                self.state.data.splice(index, 1);
                                    
                                self.setState({
                                    data : self.state.data
                                }) 
                                load()               
                            }
                               
                            
                        }
                    })     
                }
            }
            return (
                <button type="button" className="btn btn-danger-ew" data-key={row.id} onClick={((e) => handleRemove(e,row))} ><i className="far fa-trash-alt"></i></button>
            );
        }
        
        const index = (cell, row, enumObject, index) => {
            return (<div>{index+1}</div>)  
        }
    
        const cellEditProp = {
            mode: "click",
            blurToSave: true,
            beforeSaveCell: onBeforeSaveCell, // a hook for before saving cell
            afterSaveCell: onAfterSaveCell,
        };

        const  ActionLink = () => {
            const self  = this
            let handleAdd = (e) =>  {
                e.preventDefault();

                if($('input[name=variable]').val()!=''){    
                    $.ajax({
                        url:"index.php?r=setting/print-manual-add",
                        type:'POST',
                        data:{variable:$('input[name=variable]').val(),meaning:$('input[name=detail]').val()},
                        dataType:'JSON',
                        async:false,
                        success:function(response){                        
                            
                            self.state.data.push(
                                { 
                                    id:response.id,
                                    variable:response.variable,
                                    meaning:response.meaning,
                                }
                            )
                            self.setState({
                                data : self.state.data
                            })
                            load()
                        }
                    })   

                }

                
            }
  

            return (
                <button type="button" onClick={handleAdd} className="btn btn-primary">
                    <i className="far fa-plus-square"></i> ADD
                </button>
            );
        };
        const footerData = [
            [
                {
                label: 'Total value',
                columnIndex: 1,
                align: 'right',
                    formatter: () => {                    
                        return (
                            <input type="text" className="form-control" name="variable" /> 
                        );
                    }
                },
                {
                label: 'Total value',
                columnIndex: 2,
                align: 'right',
                    formatter: () => {                    
                        return (
                            <input type="text" className="form-control" name="detail" /> 
                        );
                    }
                },
                {
                label: 'Total value',
                columnIndex: 3,
                align: 'right',
                    formatter: () => {             
                        return (
                            <ActionLink /> 
                        );
                    }
                }
            ]
        ];
        return (
            <BootstrapTable data={this.state.data} cellEdit={cellEditProp} footerData={ footerData } footer pagination search>
                <TableHeaderColumn  isKey dataField='id' dataFormat={index}  width="50" >#</TableHeaderColumn>
                <TableHeaderColumn  dataField='variable' dataSort={true} width="250" editable={ { type: 'text', attrs: attrs } }>Variable</TableHeaderColumn>
                <TableHeaderColumn dataField='meaning' editable={ { type: 'text', attrs: attrs } }>Meaning</TableHeaderColumn>
                <TableHeaderColumn dataAlign="center" dataField="button" width="100" editable={ false }  dataFormat={(cell,row) => buttonRemove(cell, row)} >Delete</TableHeaderColumn>
            </BootstrapTable>
        );
    }
}

 

class ReachData extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            data : this.props.dataArray
        }
    }  

    render() {
        const indexN = (cell, row, enumObject, index) => {
            return (<div>{index+1}</div>) 
        }

        const selectAll = (cell, row) => {
            const handleFocus = (e,data) =>{
                e.target.select();
                document.execCommand('copy');
                $.notify({
                                    // options
                                    icon: 'far fa-clone',
                                    message: 'Copied ! ',                         
                                },{
                                    // settings
                                    type: 'info',
                                    delay: 1500,
                                    z_index:3000,
                                    placement: {
                                        from: "top",
                                        align: "center"
                                    }
                                });
            }
            return (
                <input className="text-info no-border" onDoubleClick={(cell,row) => handleFocus(cell, row)} value={ row.variable } readOnly="readonly"/>
            )
        }
   
        return (
            <BootstrapTable data={this.state.data} pagination search>
                <TableHeaderColumn  isKey dataField='id' dataFormat={indexN} width="50" >#</TableHeaderColumn>
                <TableHeaderColumn  dataField='variable' dataSort={true} width="250" dataFormat={(cell,row) => selectAll(cell, row)}  >Variable</TableHeaderColumn>
                <TableHeaderColumn dataField='meaning'>Meaning</TableHeaderColumn>           
            </BootstrapTable>
        );
    }
}

var load = () => {
    ReactDOM.render(
        <ReachData dataArray={dataTable[0]} />,
        document.getElementById('RenderTable')
    );
}
load();

ReactDOM.render(
    <EditTable dataArray={dataTable[0]} />,
    document.getElementById('RenderEditTable')
);
 



</script>