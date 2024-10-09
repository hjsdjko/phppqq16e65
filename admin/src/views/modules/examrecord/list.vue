<template>
  <div class="main-content" :style='{"minHeight":"calc(100vh - 200px)","padding":"20px 0 30px","margin":"0 auto","color":"#666","background":"none","width":"calc(100% - 60px)","fontSize":"14px","height":"100%"}'>
    <!-- 列表页 -->
    <template v-if="!showFlag">
      <el-form :style='{"border":"0px solid #ceddee","minHeight":"100px","padding":"10px 0px","boxShadow":"0 0px 0px 0px rgba(100,100,100,.05)","margin":"0px","overflow":"hidden","borderRadius":"20px 20px","flexWrap":"wrap","background":"none","display":"flex","fontSize":"inherit","justifyContent":"center"}' :inline="true" :model="searchForm" class="center-form-pv">
        <el-row :style='{"padding":"0px","boxShadow":"0 0px 0px 0px rgba(115,108,203,.23)","margin":"0 0px 0px 0","borderRadius":"0px","alignItems":"center","flexWrap":"wrap","background":"none","display":"flex","width":"auto","fontSize":"inherit","justifyContent":"flex-end","order":"2"}'>
			<div :style='{"margin":"0 5px 10px 0","fontSize":"inherit","display":"inline-block"}'>
			  <label :style='{"margin":"0 5px 0 0","color":"inherit","display":"inline-block","lineHeight":"40px","fontSize":"inherit","fontWeight":"500","height":"40px"}' class="item-label">试卷名称</label>
			  <el-input v-model="searchForm.papername" placeholder="试卷名称名称" clearable></el-input>
			</div>
			<el-button class="search" :style='{"border":"0","cursor":"pointer","padding":"0 24px ","margin":"0 0 10px","color":"#333","borderRadius":"6px","background":"#b3d8fc","width":"auto","fontSize":"inherit","height":"40px"}' type="success" @click="search()">
				<span class="icon iconfont icon-chakan18" :style='{"margin":"0 2px","fontSize":"inherit","color":"inherit","display":"none","height":"auto"}'></span>
				搜索
			</el-button>
		</el-row>
        <el-row class="actions" :style='{"boxShadow":"0 0px 0px 0px rgba(115,108,203,.23)","padding":"0 20px 0 0","margin":"0px 0 0px","color":"#333","alignItems":"center","textAlign":"left","display":"flex","justifyContent":"flex-start","borderRadius":"0px","flexWrap":"wrap","background":"none","flex":"1","width":"auto","fontSize":"14px","order":"1"}'>
          <download-excel v-if="isAuth('examrecord','导出')" class = "export-excel-wrapper" :data = "dataList" :fields = "json_fields" name = "考试记录.xls">
			<!-- 导出excel -->
			<el-button class="btn18" type="success">
				<span class="icon iconfont icon-shangchuan1" :style='{"margin":"0 2px","color":"#b3d8fc","left":"10px","display":"inline-block","fontSize":"24px","position":"absolute","fontWeight":"500"}'></span>
				导出
			</el-button>
          </download-excel>
		  <el-button class="btn18" v-if="isAuth('examrecord','打印')" type="success" @click="printJson">
		  	<span class="icon iconfont icon-dayin6" :style='{"margin":"0 2px","color":"#b3d8fc","left":"10px","display":"inline-block","fontSize":"24px","position":"absolute","fontWeight":"500"}'></span>
		  	打印
		  </el-button>
        </el-row>
      </el-form>

	<div :style='{"padding":"0px","boxShadow":"inset 0px 0px 0px 0px #E8EFF6","borderColor":"#b3d8fc","margin":"0 0 0px","borderRadius":"0px","background":"rgba(255,255,255,.8)","borderWidth":"1px","width":"100%","borderStyle":"solid"}'>
        <el-table
		  :stripe='true'
		  :style='{"padding":"0","borderColor":"#b3d8fc","color":"inherit","borderRadius":"0px","borderWidth":"0px 0px 0 0px","background":"none","width":"100%","fontSize":"inherit","borderStyle":"solid"}'
          :data="dataList"
          :border='true'
          v-loading="dataListLoading"
          @selection-change="selectionChangeHandler"
          style="width: 100%;"
        >
          <el-table-column :resizable='true' :sortable='true' prop="username" header-align="center" align="center" sortable label="姓名"></el-table-column>
          <el-table-column
		    :resizable='true' :sortable='true'
            prop="papername"
            header-align="center"
            align="center"
            sortable
            label="试卷名称"
          ></el-table-column>
          <el-table-column
		    :resizable='true' :sortable='true'
            prop="myscore"
            header-align="center"
            align="center"
            sortable
            label="在线考试得分"
          >
            <template slot-scope="scope">
              <el-tag v-if="scope.row.myscore==0&&scope.row.ismark==0" type="danger">{{scope.row.myscore}}</el-tag>
              <el-tag v-else-if="scope.row.myscore>0&&scope.row.ismark==0" type="success">{{scope.row.myscore}}</el-tag>
              <el-tag v-else-if="scope.row.ismark>0" type="warning">批阅中</el-tag>
            </template>
          </el-table-column>
		  <el-table-column
		    :resizable='true' :sortable='true'
		    prop="accuracy"
		    header-align="center"
		    align="center"
		    sortable
		    label="准确率"
		  >
		    <template slot-scope="scope">
		      <el-tag type="success" v-if="scope.row.ismark==0">{{(scope.row.accuracy * 100).toFixed(0)}}%</el-tag>
			  <el-tag type="warning" v-if="scope.row.ismark>0">/</el-tag>
		    </template>
		  </el-table-column>
		  <el-table-column
		    :resizable='true' :sortable='true'
		    prop="accuracy"
		    header-align="center"
		    align="center"
		    sortable
		    label="错误率"
		  >
		    <template slot-scope="scope">
		      <el-tag type="danger" v-if="scope.row.ismark==0">{{((1 - Number(scope.row.accuracy)) * 100).toFixed(0)}}%</el-tag>
			  <el-tag type="warning" v-if="scope.row.ismark>0">/</el-tag>
		    </template>
		  </el-table-column>
          <el-table-column
            header-align="center"
            align="center"
            width="150"
            label="操作"
          >
            <template slot-scope="scope">
			  <el-button class="view" :style='{"cursor":"pointer","padding":"0 8px 0 30px","boxShadow":"inset 0px 0px 0px 0px rgba(244,100,130,.3)","margin":"0 2px 5px 0","borderColor":"#fff","color":"#333","minWidth":"40px","outline":"none","borderRadius":"20px","background":"none","borderWidth":"0px","width":"auto","fontSize":"inherit","lineHeight":"24px","position":"relative","borderStyle":"solid","height":"24px"}' type="success" @click="addOrUpdateHandler(scope.row)">
			  	<span class="icon iconfont icon-chakan5" :style='{"margin":"0 0 0 4px","fontSize":"24px","position":"absolute","color":"#6daae6","left":"0","height":"auto"}'></span>
			  	详情
			  </el-button>
			  <el-button class="edit" :style='{"cursor":"pointer","padding":"0 8px 0 30px","boxShadow":"inset 0px 0px 0px 0px rgba(244,100,130,.3)","margin":"0 2px 5px 0","borderColor":"#fff","color":"#333","minWidth":"40px","outline":"none","borderRadius":"20px","background":"none","borderWidth":"0px","width":"auto","fontSize":"inherit","lineHeight":"24px","position":"relative","borderStyle":"solid","height":"24px"}' v-if="isAuth('examrecord','批卷')&&scope.row.ismark>0" type="primary" @click="gradeClick(scope.row)">
			  	<span class="icon iconfont icon-xiugai19" :style='{"margin":"0 0 0 4px","fontSize":"24px","position":"absolute","color":"#45B6B0","left":"0","height":"auto"}'></span>
			  	批卷
			  </el-button>
            </template>
          </el-table-column>
        </el-table>
	</div>
        <el-pagination
          @size-change="sizeChangeHandle"
          @current-change="currentChangeHandle"
          :current-page="pageIndex"
          :page-sizes="[10, 50, 100, 200]"
          :page-size="pageSize"
          :total="totalPage"
          :layout="layouts.join()"
          prev-text="上一页"
          next-text="下一页"
          :hide-on-single-page="false"
          :style='{"border":"1px solid #e1e1e1","padding":"10px 0","margin":"20px 0 0","whiteSpace":"nowrap","color":"inherit","textAlign":"center","background":"#eff3f9","width":"100%","fontSize":"inherit","fontWeight":"500"}'
        ></el-pagination>
    </template>
    <add-or-update v-if="showFlag" :parent="this" ref="addOrUpdate"></add-or-update>
	<el-dialog title="批卷" :visible.sync="gradeVisible" fullscreen>
		<el-card v-for="(item,index) in gradeList" :key="index" style="width: 90%;margin: 10px auto">
			<div style="padding: 20px;box-sizing:border-box;border-bottom:1px solid #eee">
				{{index + 1}}、{{item.questionname}} （ {{item.score}} ）				<el-tag type="success" v-if="item.type==0">单选题</el-tag>
				<el-tag type="warning" v-if="item.type==1">多选题</el-tag>
				<el-tag type="info" v-if="item.type==2">判断题</el-tag>
				<el-tag type="primary" v-if="item.type==3">填空题</el-tag>
				<el-tag type="danger" v-if="item.type==4">主观题</el-tag>
			</div>
			<div style="padding: 10px;box-sizing:border-box">
				考生答案：{{item.myanswer}}
			</div>
			<div style="padding: 10px;box-sizing:border-box" v-if="item.type!=4">
				正确答案：{{item.answer}}
			</div>
			<div style="padding: 20px;box-sizing:border-box">
				解析：{{item.analysis}}
			</div>
			<div style="padding: 20px;box-sizing:border-box;display:flex;align-items:center" v-if="item.type==4">
				评分：<el-input-number v-model="item.myscore" :min="0" :max="100"></el-input-number>
			</div>
		</el-card>
		<span slot="footer" class="dialog-footer">
			<el-button @click="gradeVisible=false">取 消</el-button>
			<el-button type="primary" @click="gradeSave">确 定</el-button>
		</span>
	</el-dialog>
  </div>
</template>
<script>
import AddOrUpdate from "./add-or-update";
export default {
  data() {
    return {
	  layouts: ["total","prev","pager","next","sizes","jumper"],
      searchForm: {
        key: ""
      },
      dataList: [],
      pageIndex: 1,
      pageSize: 10,
      totalPage: 0,
      dataListLoading: false,
      dataListSelections: [],
      showFlag: false,
      //导出excel
        json_fields: {
        "姓名": "username",    //常规字段
        "试卷名称": "papername",    //常规字段
        "总分": "myscore",    //常规字段
        },
        json_meta: [
          [
            {
              " key ": " charset ",
              " value ": " utf- 8 "
            }
          ]
        ],
		gradeList:[],
		gradeVisible:false
    };
  },
  mounted() {
    this.init();
    this.getDataList();
  },
  components: {
    AddOrUpdate
  },
  methods: {
    init() {},
    search() {
      this.pageIndex = 1;
      this.getDataList();
    },
    // 获取数据列表
    getDataList() {
      this.dataListLoading = true;
      var params = {
        page: this.pageIndex,
        limit: this.pageSize
      };
      if (this.searchForm.papername) {
        params["papername"] = `%${this.searchForm.papername}%`;
      }
      this.$http({
        url: this.$api.examrecordgroupby,
        method: "get",
        params: params
      }).then(({ data }) => {
        if (data && data.code === 0) {
          this.dataList = data.data.list;
          this.totalPage = data.data.total;
        } else {
          this.dataList = [];
          this.totalPage = 0;
        }
        this.dataListLoading = false;
      });
    },
    // 每页数
    sizeChangeHandle(val) {
      this.pageSize = val;
      this.pageIndex = 1;
      this.getDataList();
    },
    // 当前页
    currentChangeHandle(val) {
      this.pageIndex = val;
      this.getDataList();
    },
    // 多选
    selectionChangeHandler(val) {
      this.dataListSelections = val;
    },
    addOrUpdateHandler(row) {
      this.showFlag = true;
      this.$nextTick(() => {
        this.$refs.addOrUpdate.init(row);
      });
    },
    async printJson() {
      //通过getdata调用后台接口获取数据封装到res
      this.list = this.dataList;
      let data = []
      for (let i=0; i < this.list.length; i++) {
          data.push({
          username: this.list[i].username,
          papername: this.list[i].papername,
          myscore: this.list[i].myscore,
        })
      }
      printJS({
        printable: data,
        properties: [
      {
        field: 'username',
        displayName: '姓名',
        columnSize: 1
      },
      {
        field: 'papername',
        displayName: '试卷名称',
        columnSize: 1
      },
      {
        field: 'myscore',
        displayName: '总分',
        columnSize: 1
      },
        ],
        type: 'json',
        header: '在线考试记录',
        // 样式设置
        gridStyle: 'border: 2px solid #3971A5;',
        gridHeaderStyle: 'color: red;  border: 2px solid #3971A5;'
      })
    },
	// 批卷
	gradeClick(row) {
		this.$http({
			url: `${this.$api.examrecordpage}`,
			method: 'get',
			params: {
				page:1,
				limit: 100,
				paperid: row.paperid,
				userid: row.userid
			}
		}).then(({data})=>{
			if(data&&data.code==0){
				for(let x in data.data.list){
					if(data.data.list[x].type==4){
						data.data.list[x].myscore = 0
					}
				}
				this.gradeList = data.data.list
				this.gradeVisible = true
			}
		})
	},
	gradeSave(){
		for(let i in this.gradeList){
			this.updaterecord(this.gradeList[i])
		}
		this.$message({
			message: "批卷成功",
			type: "success",
			duration: 1500,
			onClose: () => {
				this.getDataList()
				this.gradeVisible = false
			}
		});
	},
	updaterecord(item){
		item.ismark = 1
		this.$http({
			url: `${this.$api.examrecordupdate}`,
			method: 'post',
			data: item
		}).then(({data})=>{})
	},
  }
};
</script>
<style lang="scss" scoped>
//导出excel
  .export-excel-wrapper{
    display: inline-block;
  }
.form-content {
	background: transparent;
}
.table-content {
	background: transparent;
}
	
	.center-form-pv {
		.el-input {
		  width: auto;
		}
	  .el-date-editor.el-input {
	    width: auto;
	  }
	}
	
	// form
	.center-form-pv .el-input /deep/ .el-input__inner {
				border: 1px solid #b4b4b4;
				border-radius: 6px;
				padding: 0 12px;
				color: inherit;
				max-width: 180px;
				background: #fff;
				width: auto;
				font-size: 16px;
				height: 40px;
			}
	
	.center-form-pv .el-select /deep/ .el-input__inner {
				border: 1px solid #b4b4b4;
				border-radius: 6px;
				padding: 0 10px;
				box-shadow: 0 0 0px rgba(64, 158, 255, .5);
				outline: none;
				color: inherit;
				background: #fff;
				width: 160px;
				font-size: 16px;
				height: 40px;
			}
	
	.center-form-pv .el-date-editor /deep/ .el-input__inner {
				border: 1px solid #b4b4b4;
				border-radius: 6px;
				padding: 0 10px 0 30px;
				box-shadow: 0 0 0px rgba(64, 158, 255, .5);
				outline: none;
				color: inherit;
				background: #fff;
				width: 170px;
				font-size: 16px;
				height: 40px;
			}
	
	// table
	.el-table /deep/ .el-table__header-wrapper thead {
				color: inherit;
				background: #b3d8fc;
				width: 100%;
			}
	
	.el-table /deep/ .el-table__header-wrapper thead tr {
				background: none;
			}
	
	.el-table /deep/ .el-table__header-wrapper thead tr th {
				padding: 6px 0;
				background: none;
				border-color: #d1e8fe;
				border-width: 0 1px 1px 0;
				border-style: solid;
				text-align: left;
			}
	
	.el-table /deep/ .el-table__header-wrapper thead tr th .cell {
				padding: 0 10px;
				word-wrap: normal;
				color: #333;
				white-space: normal;
				font-weight: 600;
				display: flex;
				vertical-align: middle;
				font-size: 13px;
				line-height: 24px;
				text-overflow: ellipsis;
				word-break: break-all;
				width: 100%;
				align-items: center;
				position: relative;
			}
	
	
	.el-table /deep/ .el-table__body-wrapper tbody {
				padding: 0;
				width: 100%;
			}
	
	.el-table /deep/ .el-table__body-wrapper tbody tr {
				background: none;
			}
	
	.el-table /deep/ .el-table__body-wrapper tbody tr td {
				padding: 4px 0 0;
				color: inherit;
				background: none;
				font-size: inherit;
				border-color: #d1e8fe;
				border-width: 0 1px 1px 0px;
				border-style: solid;
				text-align: left;
			}
	
		.el-table /deep/ .el-table__body-wrapper tbody tr.el-table__row--striped td {
		background: rgba(238,238,238,.5);
	}
		
	.el-table /deep/ .el-table__body-wrapper tbody tr:hover td {
				padding: 4px 0 0;
				color: #666;
				background: none;
				border-color: #d1e8fe;
				border-width: 0 1px 1px 0px;
				border-style: solid;
				text-align: left;
			}
	
	.el-table /deep/ .el-table__body-wrapper tbody tr td {
				padding: 4px 0 0;
				color: inherit;
				background: none;
				font-size: inherit;
				border-color: #d1e8fe;
				border-width: 0 1px 1px 0px;
				border-style: solid;
				text-align: left;
			}
	
	.el-table /deep/ .el-table__body-wrapper tbody tr td .cell {
				padding: 0 10px;
				overflow: hidden;
				color: inherit;
				word-break: break-all;
				white-space: normal;
				line-height: 24px;
				text-overflow: ellipsis;
			}
	
	// pagination
	.main-content .el-pagination /deep/ .el-pagination__total {
				margin: 0 10px 0 0;
				color: inherit;
				font-weight: 400;
				display: inline-block;
				vertical-align: top;
				font-size: inherit;
				line-height: 28px;
				height: 28px;
			}
	
	.main-content .el-pagination /deep/ .btn-prev {
				border: none;
				border-radius: 2px;
				padding: 0 5px;
				margin: 0 5px;
				color: inherit;
				background: none;
				display: inline-block;
				vertical-align: top;
				font-size: 18px;
				line-height: 28px;
				min-width: 35px;
				height: 28px;
			}
	
	.main-content .el-pagination /deep/ .btn-next {
				border: none;
				border-radius: 2px;
				padding: 0 5px;
				margin: 0 5px;
				color: inherit;
				background: none;
				display: inline-block;
				vertical-align: top;
				font-size: 18px;
				line-height: 28px;
				min-width: 35px;
				height: 28px;
			}
	
	.main-content .el-pagination /deep/ .btn-prev:disabled {
				border: none;
				cursor: not-allowed;
				border-radius: 2px;
				padding: 0 5px;
				margin: 0 5px;
				color: #999;
				background: none;
				display: inline-block;
				vertical-align: top;
				font-size: 18px;
				line-height: 28px;
				height: 28px;
			}
	
	.main-content .el-pagination /deep/ .btn-next:disabled {
				border: none;
				cursor: not-allowed;
				border-radius: 2px;
				padding: 0 5px;
				margin: 0 5px;
				color: #999;
				background: none;
				display: inline-block;
				vertical-align: top;
				font-size: 18px;
				line-height: 28px;
				height: 28px;
			}
	
	.main-content .el-pagination /deep/ .el-pager {
				padding: 0;
				margin: 0;
				display: inline-block;
				vertical-align: top;
				font-size: inherit;
			}
	
	.main-content .el-pagination /deep/ .el-pager .number {
				cursor: pointer;
				padding: 0 4px;
				margin: 0 5px;
				color: inherit;
				background: none;
				display: inline-block;
				vertical-align: top;
				font-size: inherit;
				line-height: auto;
				text-align: center;
				height: 24px;
			}
	
	.main-content .el-pagination /deep/ .el-pager .number:hover {
				cursor: pointer;
				border: 1px solid #ddd;
				padding: 0 4px;
				margin: 0 5px;
				color: #333;
				display: inline-block;
				vertical-align: top;
				font-size: inherit;
				line-height: auto;
				border-radius: 4px;
				background: rgba(255,255,255,.5);
				text-align: center;
				height: 24px;
			}
	
	.main-content .el-pagination /deep/ .el-pager .number.active {
				cursor: default;
				border: 1px solid #ddd;
				padding: 0 4px;
				margin: 0 5px;
				color: #333;
				display: inline-block;
				vertical-align: top;
				font-size: inherit;
				line-height: auto;
				border-radius: 4px;
				background: rgba(255,255,255,.5);
				text-align: center;
				height: 24px;
			}
	
	.main-content .el-pagination /deep/ .el-pagination__sizes {
				color: inherit;
				display: inline-block;
				vertical-align: top;
				font-size: inherit;
				line-height: 28px;
				height: 28px;
			}
	
	.main-content .el-pagination /deep/ .el-pagination__sizes .el-input {
				margin: 0 5px;
				color: inherit;
				width: 100px;
				font-size: inherit;
				position: relative;
			}
	
	.main-content .el-pagination /deep/ .el-pagination__sizes .el-input .el-input__inner {
				border: 0px solid #ddd;
				cursor: pointer;
				padding: 0 25px 0 8px;
				color: inherit;
				display: inline-block;
				font-size: inherit;
				line-height: 28px;
				border-radius: 3px;
				outline: 0;
				background: none;
				width: 100%;
				text-align: center;
				height: 28px;
			}
	
	.main-content .el-pagination /deep/ .el-pagination__sizes .el-input span.el-input__suffix {
				top: 0;
				position: absolute;
				right: 0;
				height: 100%;
			}
	
	.main-content .el-pagination /deep/ .el-pagination__sizes .el-input .el-input__suffix .el-select__caret {
				cursor: pointer;
				color: #C0C4CC;
				width: 25px;
				font-size: 14px;
				line-height: 28px;
				text-align: center;
			}
	
	.main-content .el-pagination /deep/ .el-pagination__jump {
				margin: 0 0 0 24px;
				color: inherit;
				display: inline-block;
				vertical-align: top;
				font-size: inherit;
				line-height: 28px;
				height: 28px;
			}
	
	.main-content .el-pagination /deep/ .el-pagination__jump .el-input {
				border-radius: 3px;
				padding: 0 2px;
				margin: 0 2px;
				color: inherit;
				display: inline-block;
				width: 50px;
				font-size: inherit;
				line-height: 18px;
				position: relative;
				text-align: center;
				height: 28px;
			}
	
	.main-content .el-pagination /deep/ .el-pagination__jump .el-input .el-input__inner {
				border: 1px solid #eee;
				cursor: pointer;
				padding: 0 0px;
				color: inherit;
				display: inline-block;
				font-size: inherit;
				line-height: 24px;
				border-radius: 3px;
				outline: 0;
				background: #fff;
				width: auto;
				text-align: center;
				height: 24px;
			}
	
	.center-form-pv .search {
				border: 0;
				cursor: pointer;
				border-radius: 6px;
				padding: 0 24px ;
				margin: 0 0 10px;
				color: #333;
				background: #b3d8fc;
				width: auto;
				font-size: inherit;
				height: 40px;
			}
	
	.center-form-pv .search:hover {
				opacity: 0.8;
			}
	
	.center-form-pv .actions .btn18 {
				border: 1px solid #b3d8fc80;
				cursor: pointer;
				padding: 4px 10px 0 45px;
				margin: 0px 2px 10px;
				color: #333;
				font-size: inherit;
				line-height: 30px;
				border-radius: 4px;
				outline: none;
				background: rgba(255,255,255,.6);
				width: auto;
				position: relative;
				min-width: 66px;
				height: auto;
			}
	
	.center-form-pv .actions .btn18:hover {
				opacity: 0.8;
			}
	
	.el-table /deep/ .el-table__body-wrapper tbody tr td .view {
				cursor: pointer;
				padding: 0 8px 0 30px;
				margin: 0 2px 5px 0;
				color: #333;
				font-size: inherit;
				border-color: #fff;
				line-height: 24px;
				border-radius: 20px;
				box-shadow: inset 0px 0px 0px 0px rgba(244,100,130,.3);
				outline: none;
				background: none;
				width: auto;
				border-width: 0px;
				position: relative;
				border-style: solid;
				min-width: 40px;
				height: 24px;
			}
	
	.el-table /deep/ .el-table__body-wrapper tbody tr td .view:hover {
				border-color: #23b7e5;
				border-width: 0px;
				opacity: 0.8;
				border-style: solid;
			}
	
	.el-table /deep/ .el-table__body-wrapper tbody tr td .edit {
				cursor: pointer;
				padding: 0 8px 0 30px;
				margin: 0 2px 5px 0;
				color: #333;
				font-size: inherit;
				border-color: #fff;
				line-height: 24px;
				border-radius: 20px;
				box-shadow: inset 0px 0px 0px 0px rgba(244,100,130,.3);
				outline: none;
				background: none;
				width: auto;
				border-width: 0px;
				position: relative;
				border-style: solid;
				min-width: 40px;
				height: 24px;
			}
	
	.el-table /deep/ .el-table__body-wrapper tbody tr td .edit:hover {
				border-color: #45B6B0;
				border-width: 0px;
				opacity: 0.8;
				border-style: solid;
			}
	// list one
	.one .list1-view {
				cursor: pointer;
				padding: 0 8px 0 30px;
				margin: 0px 5px 5px 0;
				color: #333;
				font-size: inherit;
				border-color: #fff;
				line-height: 24px;
				border-radius: 20px;
				box-shadow: inset 0px 0px 0px 0px rgba(244,100,130,.3);
				outline: none;
				background: none;
				width: auto;
				border-width: 0px;
				position: relative;
				border-style: double;
				min-width: 40px;
				height: 24px;
			}
	
	.one .list1-view:hover {
				border-color: #23b7e5;
				border-width: 0px;
				opacity: 0.8;
				border-style: solid;
			}
</style>
