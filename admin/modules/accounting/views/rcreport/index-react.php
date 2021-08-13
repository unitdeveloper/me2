<?php admin\assets\ReactAsset::register($this); ?>

<?php $Options = ['depends' => [admin\assets\ReactAsset::className()]]; ?> 
<?php $this->registerJsFile('//npmcdn.com/react-bootstrap-table/dist/react-bootstrap-table.min.js',$Options) ?>
<?php $this->registerCssFile('//npmcdn.com/react-bootstrap-table/dist/react-bootstrap-table-all.min.css', $Options); ?>
<!-- //http://allenfang.github.io/react-bootstrap-table/example.html#basic  -->


<?php //$this->registerJsFile('//unpkg.com/react-table@latest/react-table.js#1',$Options) ?>
<?php //$this->registerCssFile('//unpkg.com/react-table@5.5.0/react-table.css', $Options); ?>
<?php //$this->registerCssFile('//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css', $Options); ?>
<!-- https://codepen.io/aaronschwartz/pen/WOOPRw?editors=0010 -->


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

// let dataTable = [];

$('body').on('click','a.modal-invoice-convert',function(){
    $('#modal-invoice-convert').modal('show');
    let id = $(this).data('key')

    $.ajax({
        url:"index.php?r=accounting/rcreport/get-invoice-line&id="+id,
        type:'GET',
        data:{id:id},
        dataType:'JSON',
        async:false,
        success:function(response){

            // dataTable = [];
						// dataTable.push(response.data);
						$('input[name="id"]').val(response.id);
						$('#document_no').text(response.no);
						$('input[name="RcInvoiceHeader[no_]"]').val(response.new_doc);
						$('input[name="RcInvoiceHeader[cust_no_]"]').val(response.cust);
						$('input[name="RcInvoiceHeader[cust_code]"]').val(response.cust_code);
						$('input[name="RcInvoiceHeader[cust_name_]"]').val(response.cust_name);
						$('textarea[name="RcInvoiceHeader[cust_address]"]').text(response.cust_addr);
						$('input[name="status"]').val(response.status);
						$('div.field-rcinvoiceheader-sale_id select').val(response.sale_id);
 
            ReactDOM.render(
                <EditTable dataTable={response.data}/>,
                document.getElementById('render-invoice')
            );
            
        }
    })
     
})

 
class Checkbox extends React.Component {
  componentDidMount() { this.update(this.props.checked); }
  componentWillReceiveProps(props) { this.update(props.checked); }
  update(checked) {
    ReactDOM.findDOMNode(this).indeterminate = checked === 'indeterminate';
  }
  render() {
		 
    return (
      <input className='react-bs-select-all'
        type='checkbox'
        name={ 'checkbox' + this.props.rowIndex }
        id={ 'checkbox' + this.props.rowIndex }
        checked={ this.props.checked } 
        onChange={ this.props.onChange } />
    );
  }
}

const VatList = [];

$.ajax({
    url:"index.php?r=accounting/rcreport/get-vat-list",
    type:'GET',
    dataType:'JSON',
    async:false,
    success:function(response){
			VatList.push(response.data);
    }
})

class VatType extends React.Component {
  constructor(props) {
				super(props);
				this.state = {
					vat: VatList[0]
				}
		} 

  render() {
 

		const handleChange = (e) =>  {
			console.log(e.target.value);
			grandTotal()
		}
    return (
			<div className="vatTotalText">			  
			 <select name="vatPercent" onChange={ handleChange } className="form-control">
			 {
				 this.state.vat.map(model=>(
					<option key={ model.id } value={model.value} >{model.label}</option>
				 ))				
			 }				 
				</select >
			 </div>
    );
  }
}
 
const grandTotal = () => {
	let total = 0;
	// $.each( $('.react-bs-container-body').find('tr').find('div.sumLine'), function(indexInArray, e){ 
	// 	total += $(e).attr('data') * 1;						
	// 		//console.log($(e).attr('data'));
	// });		
	$.each( $('input[type="checkbox"]:checked').closest('tr').find('div.sumLine'), function(indexInArray, e){ 
		total += $(e).attr('data') * 1;						
			console.log($(e).attr('data'));
	});	
	
	
	let Vat  	= $('select[name="vatPercent"]').val();
	let vatTotal 	= total * Vat /100
	let grandTotal = vatTotal + total

	$('.total').text(number_format(total.toFixed(2))).attr('data',total);
	$('.vatTotal').text(number_format(vatTotal.toFixed(2))).attr('data',vatTotal);
	$('.grandTotal').text(number_format(grandTotal.toFixed(2))).attr('data',grandTotal);
}


class EditTable extends React.Component {
    constructor(props) {
				super(props);
				this.onSelectAll = this.onSelectAll.bind(this);
				this.customMultiSelect = this.customMultiSelect.bind(this);
        this.state = {            
						data : this.props.dataTable,
				}
 
			
 
		} 
	
		
		onSelectAll(isSelected,row) {
				let i = 0;
				if (isSelected) {
					this.props.dataTable.map(element => {						
						$('input[id=checkbox'+i+']').val(element.id);
						i++
					});
					return this.props.dataTable.map(model => model.id);
				} else {
					this.props.dataTable.map(element => {						
						$('input[id=checkbox'+i+']').val(element.id);
						i++
					});
					return [];
				}
		}
	 
		customMultiSelect(props) {
			const { type, checked, disabled, onChange, rowIndex} = props;		  
			let id = [];
			this.props.dataTable.map(element => { 
				id.push(element.id);				
			});
			/*
			* If rowIndex is 'Header', means this rendering is for header selection column.
			*/
			
			if (rowIndex === 'Header') {

				return (
					<div className='checkbox-personalized'>
						<Checkbox {...props}/>
						<label htmlFor={ 'checkbox' + rowIndex }>
							<div className='check'></div>
						</label>
					</div>
					);

			} else {

				return (
					<div className='checkbox-personalized'>
						<input
							type={ type }
							name={ 'line[]'}
							id={ 'checkbox' + rowIndex }			
							value={ id[rowIndex] }					 			
							checked={ checked }
							disabled={ disabled }
							onChange={ e=> onChange(e, rowIndex) }
							ref={ input => {
								if (input) {
									input.indeterminate = props.indeterminate;
								}
							} }/>						 
					</div>				
				);
			}

		}  

    

    render() {

				

				const onRowSelect = (row, isSelected, e) =>  {
					// Set id to input[type=checkbox]  (ใส่ value ใน checkbox (เมื่อมีการคลิก))
					grandTotal()
					e.target.value = row.id 
				}
        const index = (cell, row, enumObject, index) => {
            return (<div>{index+1}</div>)  
        }

				 
			 
				const inputItext = (cell,row,index) => {
					const handleFocus = (e,data) =>{
                e.target.select();
						}

					const reCal = (e,data) => {
						let { value,name } = e.target
						let id = e.target.getAttribute('data-key')
						 
						var index = this.props.dataTable.indexOf(data);
						var sumLine = $('input[name="price['+ id +']"]').val() * value
						let total = number_format(sumLine.toFixed(2))

						$('input[name="'+ name +'"]').closest('tr').find('.sumLine').text(total).attr('data',sumLine);
						grandTotal()	 			
						 
					}
				
					return (
						<input type="number" step="any" 
						className="form-control text-right" 
						defaultValue={ row.qty *1 } 
						name={'qty[' + row.id + ']'} 
						data-key={ row.id }
						onClick={handleFocus} 
						onChange={((e) => reCal(e,row))} />
					)
				}

				const inputItextPrice = (cell,row,index) => {

					

					const handleFocus = (e,data) =>{
                e.target.select();
					}
					
					const reCal = (e,data) => {
						let { value,name } = e.target
						let id = e.target.getAttribute('data-key')
						 
						var index = this.props.dataTable.indexOf(data);
						
						var sumLine = $('input[name="qty['+ id +']"]').val() * value
						let total = number_format(sumLine)

						$('input[name="'+ name +'"]').closest('tr').find('.sumLine').text(total).attr('data',sumLine);
						
						grandTotal()	 			

					   					
					  


					}
					
					return (
						<input type="number" step="any" className="form-control text-right" 
						defaultValue={ row.price *1 } 
						name={'price[' + row.id + ']'} 
						data-key={ row.id }
						onClick={handleFocus} 
						onChange={((e) => reCal(e,row))}  />
					)
				}

				const sumTotal = (cell,row) => {
					let total = row.qty * row.price
					let grandTotal = number_format(total.toFixed(2))
					
					return (
						<div className="text-right sumLine" data={ total }>{grandTotal}</div>
					)
				}

	 

				const footerData = [
            [
                {
                label: 'Total value',
                columnIndex: 1,
                align: 'right',
                    formatter: () => {                    
                        return (
													<div></div>
                        );
                    }
                },
                {
                label: 'Total value',
                columnIndex: 4,
                align: 'right',
                    formatter: () => {                    
                        return (
														<div>
															<div className="totalText"> Total </div>
															<VatType />
															<div className="grandTotalText"> Grand Total </div>
														</div>
                        );
                    }
                },
                {
                label: 'Total value',
                columnIndex: 5,
                align: 'right',
								formatter: (tableData) => {
										let total = 0;
										let vatTotal = 0;
										let grandTotal = 0;
										for (let i = 0, tableDataLen = tableData.length; i < tableDataLen; i++) {
											total += tableData[i].qty * tableData[i].price;
										}
										vatTotal = (total * 7)/100
										grandTotal = vatTotal + total
										return (
											<div className="sum">
												<div className="total">{ number_format(total.toFixed(2)) }</div>
												<div className="vatTotal">{ number_format(vatTotal.toFixed(2)) }</div>
												<div className="grandTotal">{ number_format(grandTotal.toFixed(2)) }</div>
											</div>
										);
									}
                }
            ]
				];
				
				const dataId = () => {
					let value = []			
					this.props.dataTable.map(element => {
						value.push(element.id)
					});
					return value
				}
			 
				const selectRowProp = {
					mode: 'checkbox',
					bgColor: '#fcfeff',
					//clickToSelect: true, 
					customComponent: this.customMultiSelect,
					onSelect: onRowSelect,
					onSelectAll: this.onSelectAll,
					//showOnlySelected: true,			 
					
					selected:dataId() 
				};	
		 
				
        return (
						
            <BootstrapTable data={this.props.dataTable} selectRow={ selectRowProp } footerData={ footerData } footer>
                <TableHeaderColumn dataField='id' 		dataFormat={index}  width="50" isKey  >#</TableHeaderColumn>
                <TableHeaderColumn dataField='code' 	width="250" ><?=Yii::t('common','Items')?></TableHeaderColumn>
                <TableHeaderColumn dataField='desc'  	><?=Yii::t('common','Description')?></TableHeaderColumn>
								<TableHeaderColumn dataField='qty' 		dataFormat={inputItext} width="150"><?=Yii::t('common','Quantity')?></TableHeaderColumn>
								<TableHeaderColumn dataField='price' 	dataFormat={inputItextPrice} width="150"><?=Yii::t('common','Price')?></TableHeaderColumn>
								<TableHeaderColumn dataField='total' 	dataFormat={sumTotal} width="150"><?=Yii::t('common','Total')?></TableHeaderColumn>
            </BootstrapTable>
           
        );

    }

}

 
// let load = () => {
//     ReactDOM.render(
//                 <EditTable dataTable={dataTable[0]}/>,
//                 document.getElementById('render-invoice')
//             );
// }
</script>






 