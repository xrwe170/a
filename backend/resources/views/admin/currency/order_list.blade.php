@extends('agent.layadmin')

@section('page-head')

@endsection

@section('page-content')

<style>
    .layui-form-item{
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }
    .layui-form-label{
        width: auto;
    }
    .layui-form-item .layui-inline{
        display: flex;    
    }
    
</style>
    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layadmin-userfront-formlist">
                <div class="layui-form-item">
                    <!--<div class="layui-inline">-->
                    <!--    <label class="layui-form-label">ID</label>-->
                    <!--    <div class="layui-input-block">-->
                    <!--        <input type="text" name="id" placeholder="请输入" autocomplete="off" class="layui-input">-->
                    <!--    </div>-->
                    <!--</div>-->
                    <div class="layui-inline">
                        <label class="layui-form-label">用户</label>
                        <div class="layui-input-block" style="margin-left: 10px;">
                            <input type="text" name="mobile" placeholder="请输入" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline" style="margin-left: 10px;">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline" style="margin-left: 10px;">
                           <select name="status"  class="layui-input">
                               <option value="">选择状态</option>
                               <option value="1">进行中</option>
                               <option value="2">到期</option>
                           </select>
                        </div>
                    </div>
                    <div class="layui-inline" style="margin-left: 10px;">
                        <button class="layui-btn layuiadmin-btn-useradmin" lay-submit lay-filter="searchtj">
                            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="layui-card-body">

                <table id="boss" lay-filter="boss"></table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/html" id="barDemo">
       <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="getsons"><i class="layui-icon layui-icon-friends"></i>下级</a>
    </script>

    <script>
        layui.use(['table','form'], function(){
            // console.log(layui.setter.base)
            var table = layui.table;
            var $ = layui.jquery;
            var form = layui.form;

             //第一个实例
            table.render({
                elem: '#boss'
                ,url: '/admin/currency/deposit_order/list' //数据接口
                ,page: true //开启分页
                ,id:'mobileSearch'
                ,cols: [[ //表头
                    {field: 'id', title: 'ID', width:80, sort: true}
                    
                    ,{field: 'account_number', title: '用户', width:150}
                     ,{field: 'currency_name', title: '币种', width:100}
                    ,{field:'day_rate',title: '日利率(%)', width:100,}
                    ,{field: 'total_rate', title: '总利率(%)', width:100}
                    ,{field: 'amount', title: '存币数量', width:130}
                    ,{field: 'total_interest', title: '总利息', width:130}
                    ,{field: 'start_at', title: '开始时间', width:150}
                    ,{field: 'end_at', title: '到期时间',width:150}
                    ,{field: 'created_at', title: '创建时间',width:150}
                    ,{ title: '状态',templet:function(d){
                       
                        if(d.status==1){
                            return '<span style="color:#00c087">进行中</span>';    
                        }else if(d.status==2){
                            return '<span style="color:#f00">到期</span>'; 
                        }
                    }
                    }
                    //  ,{title:'操作',minWidth:100,toolbar: '#barDemo'}
                ]]
            });
            //监听提交
            form.on('submit(searchtj)', function(data){
                // var seller_name = data.field.seller_name
                var account_number = data.field.mobile,
                    status= data.field.status,
                    is_cancel=0;
                    if(status==2){
                        is_cancel=1;
                    }
                    if(status==3){
                        status=2;
                        is_cancel=0;
                    }
                    if(status==1 || status==""){
                        is_cancel='';    
                    }
                    // ,currency_id = $('#currency_id').val()

                table.reload('mobileSearch',{
                    where:{
                        // is_cancel:is_cancel,
                        account_number:account_number,
                    	status:status,
                        // currency_id:currency_id,
                    },
                    page: {curr: 1}         //重新从第一页开始
                });
                return false;
            });
            table.on('tool(boss)', function(obj){
                var data = obj.data;
                if(obj.event === 'getsons'){
                	table.reload('mobileSearch',{
                    where:{
	                        p_id:data.id,
	                        // currency_id:currency_id,
	                    },
	                    page: {curr: 1}         //重新从第一页开始
	                });
                }
            });
        });
    </script>
    
@endsection
