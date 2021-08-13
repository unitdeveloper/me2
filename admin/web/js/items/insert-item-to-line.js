
aVaGridView = (req,res) => {
    
    
        read_prop = (obj,prop) => {
            return obj[prop];
        }

        options = (options) => {
            
            let attr = '';
            if (options != undefined ){
                $.each(options,(tag,val) => {    
                    attr+= tag + '="' + val + '" ';        
                })
            }

           return attr;
        }

        tagHtml = (obj,props) => {
            let tag = '';
            if (props != undefined){
                if (props.tag != undefined ){ 
                    if(props.tag=='input'){
                        tag+= '<'+props.tag+' value="' + read_prop(obj,props.value) + '" name="' + props.name + '" ' + options(props.options) + ' />';
                    }else if(props.tag=='div'){
                        tag+= '<'+props.tag+'>' + props.value(obj) + '</'+props.tag+'>';
                    }else{
                        tag+= '<'+props.tag+'>' + read_prop(obj,props.value) + '</'+props.tag+'>';
                    }                    
                }else{
                    tag+= props.value(obj);
                }
            }
            return tag;
        }

 

        rawHtml = (obj,props) => {
            let tag = '';
            if (props != undefined){
                if (props.tag != undefined ){ 
                    if(props.tag=='input'){
                        tag+= '<'+props.tag+' value="' + read_prop(obj,props.value) + '" name="' + props.name + '" ' + options(props.options) + ' />';
                    }else if(props.tag=='div'){
                        tag+= '<'+props.tag+'>' + props.value + '</'+props.tag+'>';
                    }else{
                        tag+= '<'+props.tag+'>' + read_prop(obj,props.value) + '</'+props.tag+'>';
                    }
                }
            }
            return tag;
        }
        
        
        $.ajax({url: req.url,type: req.type,data: req.data,dataType:'JSON',success: function(response) {

                let data = response.data.line;
                let i = 0;
                let td = '<table ' + ((req.tableOptions != undefined)? req.tableOptions(req) : ' ') + '>\r\n';
                let fild = req.column;
                td+='   <thead>\r\n'
                td+= '      <tr ' + ((req.filterOptions!=undefined)? req.filterOptions(req) : ' ') + '>\r\n';
                    fild.forEach((el) => { 
                        td+=  ' <th ' + ((el.headOptions != undefined)? el.headOptions(req) : ' ') + '>' + el.label + '</th>\r\n';
                    });
                td+= '      </tr>\r\n';
                td+='   </thead>\r\n';
                td+='<tbody>\r\n';
                
                
                data.forEach((model) => {
                     
                    i++;
                    td+= '<tr ' +  ((req.rowOptions != undefined)? req.rowOptions(model) : ' ') + '>\r\n';
                    fild.forEach((el) => { 
                        if (el.format != undefined ){                            

                            switch (el.value) {                              
                                case 'index':
                                    td+='   <td>'+i+'</td>\r\n';
                                    break;

                                default:  
                                    if (el.format == 'html') {
                                        td+='   <td ' + options(el.contentOptions) +'>'+ tagHtml(model,el) + '</td>\r\n';        
                                    } else {
                                        td+='   <td ' + options(el.contentOptions) +'>'+ rawHtml(model,el) + '</td>\r\n';        
                                    }                                                           
                                    break;
                            }   

                        }else {

                            switch (el.value) {
                                case 'index':
                                    td+='   <td>'+i+'</td>\r\n';
                                    break;
                                
                                default:  
                                    td+='   <td ' + options(el.contentOptions) +'>'+ rawHtml(model,el) + '</td>\r\n';                                
                                    break;
                            }   
                        
                        }
                        
                    });

                    td+= '</tr>\r\n';                   

                });
                td+='</tbody>\r\n';
                td+= '</table>';
                res(td);

            }
        });
}


aVaGridView(    
    {
        url:'index.php?r=accounting/credit-note/sale-invoice-line&id=5160',
        data:{id:5160,key:19828,val:3},
        type:'POST',
        filterOptions:(res) => {
            return 'class="bg-gray"';
        },
        tableOptions:(res) => {
            return 'class="table table-hover table-striped table-bordered"';
        },
        rowOptions:(res) => {
            return 'data-key="' + res.id + '"';
        },
        column : [
            {value:'index',tag:'label',label:'#',
                headOptions:(res) => {
                    return 'style="width:80px;"';
                },
            },
            {value:'code',tag:'label',label:'item',
                headOptions:(res) => {
                    return 'style="width:190px;"';
                },
            },
            {
                label : 'amount',                
                format:'html',
                value:(res) => {
                    return '<input type="text" value="' + res.desc + '" class="form-control text-line next" name="desc" autocomplete="off"/>';
                }
            },
            { 
                value:'qty',
                label:'qty',
                tag:'input', 
                name:'qty',
                type:'text',    
                val:'number',
                headOptions:(res) => {
                    return 'style="width:80px;"';
                },                  
                options:{
                    class:'form-control text-right text-line next',
                    autocomplete:"off"
                },
                contentOptions:{
                    align:'right',
                    class:'text-right'
                }
            }, 
            { 
                value:'price',
                label:'Price',
                tag:'input', 
                name:'price',
                type:'text',    
                val:'number',  
                headOptions:(res) => {
                    return 'style="width:120px;"';
                },                
                options:{
                    class:'form-control text-right text-line next',
                    autocomplete:"off"
                },
                contentOptions:{
                    align:'right',
                    class:'text-right',
                    
                }
            },
            {              
                tag:'div', 
                label:'Amount',
                format:'html',
                headOptions:(res) => {
                    return 'style="width:102px;"';
                },
                value:(res) => {
                    return res.qty * res.price;
                },
                html:{
                    cal:{
                        formula:'qty * price',
                        type:'number',
                        digit:2
                    }
                },               
                options:{
                    class:'ew-line-total',
                    data:{
                        formula:'qty * price',
                        type:'int'
                    }
                },
                contentOptions:{
                    align:'right',
                    class:'text-right'
                }
            },
            {
                label: ' ',
                format:'html',
                headOptions:(res) => {
                    return 'style="width:70px;"';
                },
                value:(res) => {
                    return '<div class="btn btn-danger ew-delete-inv-line" data="' + res.id +'"><i class="fa fa-trash-o" aria-hidden="true"></i></div>';
                },
                contentOptions:{
                    align:'right',
                    class:'text-right'
                }
            }
        ]
    },
    (res) => {
        console.log(res);
        $('.invoice-list-render').append(res);
    }
);
