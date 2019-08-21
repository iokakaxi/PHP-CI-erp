<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Goods_ex_model extends CI_Model{

    public function goodsStyle($data,$name){
        // 导出Exl
        $this->load->library('lib_cn2pinyin');
        require_once './application/libraries/phpexcel/PHPExcel.php';
        $objPHPExcel = new \PHPExcel();
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);

        $objActSheet = $objPHPExcel->getActiveSheet();

        // 水平居中（位置很重要，建议在最初始位置）
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('G')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('H')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('I')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('J')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('K')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('L')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('M')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('N')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('O')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('P')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('Q')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('R')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('S')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('T')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objActSheet->setCellValue('A1', '商品编号');
        $objActSheet->setCellValue('B1', '商品条码');
        $objActSheet->setCellValue('C1', '商品名称');
        $objActSheet->setCellValue('D1', '商品图片');
        $objActSheet->setCellValue('E1', '商品类别');
        $objActSheet->setCellValue('F1', '规格尺码');
        $objActSheet->setCellValue('G1', '款号');
        $objActSheet->setCellValue('H1', '采购价格');
        $objActSheet->setCellValue('I1', '采购成本');
        $objActSheet->setCellValue('J1', '原价/吊牌价');
        $objActSheet->setCellValue('K1', '批发价1');
        $objActSheet->setCellValue('L1', '批发价2');
        $objActSheet->setCellValue('M1', '批发价3');
        $objActSheet->setCellValue('N1', '折扣一');
        $objActSheet->setCellValue('O1', '折扣二');
        $objActSheet->setCellValue('P1', '淘宝天猫价');
        $objActSheet->setCellValue('Q1', '淘宝天猫链接');
        $objActSheet->setCellValue('R1', '京东价格');
        $objActSheet->setCellValue('S1', '京东链接');
        $objActSheet->setCellValue('T1', '零售价');
        // 设置个表格宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(12);

        // 垂直居中
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('L')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('N')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('O')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('P')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('Q')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('R')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('S')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('T')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objPHPExcel->getActiveSheet(0)->getStyle('B')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        foreach($data as $k=>$v){
            $k +=2;
            $objActSheet->setCellValue('A'.$k, $v['number']);
            $objActSheet->setCellValue('B'.$k, $v['barCode']);
            $objActSheet->setCellValue('C'.$k, $v['name']);

            // 图片生成
            /*$objDrawing[$k] = new \PHPExcel_Worksheet_Drawing();
            $objDrawing[$k]->setPath('./data/upfile/goods/'.$v['imgName']);
            // 设置宽度高度
            $objDrawing[$k]->setHeight(70);//照片高度
            $objDrawing[$k]->setWidth(70); //照片宽度
            //设置图片要插入的单元格
            $objDrawing[$k]->setCoordinates('D'.$k);
            // 图片偏移距离
            $objDrawing[$k]->setOffsetX(12);
            $objDrawing[$k]->setOffsetY(12);
            $objDrawing[$k]->setWorksheet($objPHPExcel->getActiveSheet());*/

            $objActSheet->setCellValue('D'.$k, '');


            $objActSheet->setCellValue('E'.$k, $v['categoryName']);
            $objActSheet->setCellValue('F'.$k, $v['spec']);
            $objActSheet->setCellValue('G'.$k, $v['colorType']);
            $objActSheet->setCellValue('H'.$k, $v['purPrice']);
            $objActSheet->setCellValue('I'.$k, $v['unitCost']);
            $objActSheet->setCellValue('J'.$k, $v['diaopaiPrice']);
            $objActSheet->setCellValue('K'.$k, $v['wholesalePrice']);
            $objActSheet->setCellValue('L'.$k, $v['wholesalePrice2']);
            $objActSheet->setCellValue('M'.$k, $v['wholesalePrice3']);
            $objActSheet->setCellValue('N'.$k, $v['discountRate1']);
            $objActSheet->setCellValue('O'.$k, $v['discountRate2']);
            $objActSheet->setCellValue('P'.$k, $v['taobaoPrice']);
            $objActSheet->setCellValue('Q'.$k, $v['taobaoLink']);
            $objActSheet->setCellValue('R'.$k, $v['jingdongPrice']);
            $objActSheet->setCellValue('S'.$k, $v['jingdongLink']);
            $objActSheet->setCellValue('T'.$k, $v['salePrice']);

            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(70);
        }


        $fileName = iconv("utf-8", "gb2312", $name);
        //重命名表
        // $objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
    }
}