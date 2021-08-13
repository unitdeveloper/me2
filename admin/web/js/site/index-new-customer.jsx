class Newcustomer extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            customerAmount : 0,
            customerNew : 0,
            customerCancel : 0,
            cancelThisMonth : 0,
            text : [],
        }
    }
    
    componentWillMount(){
        this.getDataCustomer();
        
    }

    componentDidUpdate(){
       // console.log(this.state.text)        
    }

 
    
    getDataCustomer = _ => {
        var that = this;
        $.ajax({
            url:'?r=SaleOrders/ajax/customers-count',
            type:'GET',
            dataType:'JSON',
            success:function(response){
                that.setState({ customerAmount : response.count,customerNew : response.new ,customerCancel : response.cancel, cancelThisMonth : response.cancelThisMonth })
            }
        })
    }

    render () {
       
        return (
            <div className="row">
                <div className="col-md-3 col-xs-6">
                    <div className="box box-success box-solid">
                        <div className="box-header with-border">
                        <h3 className="box-title">{t.LABEL_ALL_CUSTOMER}</h3>
                        <div className="box-tools pull-right">
                            <button type="button" className="btn btn-box-tool" data-widget="remove"><i className="fa fa-times"></i></button>
                        </div>                     
                        </div>                     
                        <div className="box-body">
                            <a href="?r=customers/customer" target="_blank">{number_format(this.state.customerAmount)}</a>
                        </div>                     
                    </div>                 
                </div>
                <div className="col-md-3 col-xs-6">
                    <div className="box box-info box-solid">
                        <div className="box-header with-border">
                        <h3 className="box-title">{t.LABEL_NEW_CUSTOMER}</h3>
                        <div className="box-tools pull-right">
                            <button type="button" className="btn btn-box-tool" data-widget="remove"><i className="fa fa-times"></i></button>
                        </div>                     
                        </div>                     
                        <div className="box-body">
                            <a href="?r=customers/customer&new=true" target="_blank">{number_format(this.state.customerNew)}</a>
                        </div>                     
                    </div>                 
                </div>
                <div className="col-md-3 col-xs-6">
                    <div className="box box-warning box-solid">
                        <div className="box-header with-border">
                        <h3 className="box-title">{t.LABEL_CANCEL_CUSTOMER}</h3>
                        <div className="box-tools pull-right">
                            <button type="button" className="btn btn-box-tool" data-widget="remove"><i className="fa fa-times"></i></button>
                        </div>                     
                        </div>                     
                        <div className="box-body">
                            <a href="?r=customers/customer&SearchCustomer[status]=0" target="_blank">{number_format(this.state.customerCancel)}</a>
                        </div>                     
                    </div>                 
                </div>
                <div className="col-md-3 col-xs-6">
                    <div className="box box-danger box-solid">
                        <div className="box-header with-border">
                        <h3 className="box-title">{t.LABEL_SUSPEND_CUSTOMER}</h3>
                        <div className="box-tools pull-right">
                            <button type="button" className="btn btn-box-tool" data-widget="remove"><i className="fa fa-times"></i></button>
                        </div>                     
                        </div>                     
                        <div className="box-body">
                            <a href="?r=customers/customer&SearchCustomer[suspend]=1" target="_blank">{number_format(this.state.cancelThisMonth)}</a>
                        </div>                     
                    </div>                 
                </div>                
            </div>
        )
    }
}

ReactDOM.render(
    <Newcustomer />,
    document.getElementById('new-customer')
)