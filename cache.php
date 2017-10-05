<?php
/*
�û���Ҫ���ȶ���ĳ�����
_CachePath_        ģ�建��·��
_CacheEnable_        �Զ���������Ƿ�����δ�����Ϊ�գ���ʾ�ر��Զ��������
_ReCacheTime_        �Զ����»�����ʱ�䣬��λΪ�룬δ�����Ϊ�գ���ʾ�ر��Զ����»���
*/
error_reporting(0);
class cache
{
	var $cachefile;
	var $cachefilevar;

	function cache()
	{
        //���ɵ�ǰҳ��Cache���ļ��� $this->cachefilevar ���ļ��� $this->cachefile
        //��̬ҳ�Ĳ�����ͬ��Ӧ��Cache�ļ�Ҳ��ͬ������ÿһ����̬ҳ������Cache�ļ�������ͬ���ļ�����ֻ����չ����ͬ
        $s=array(".","/");$r=array("_","");
        $this->cachefilevar=str_replace($s,$r,$_SERVER["SCRIPT_NAME"])."_".$_GET[_ActionVar_];
        $this->cachefile=$this->cachefilevar.".".md5(1);
	}

	//ɾ����ǰҳ/ģ��Ļ���
	function delete()
	{
        //ɾ����ǰҳ�Ļ���
        $d = dir(_CachePath_);
        $strlen=strlen($this->cachefilevar);
        //���ص�ǰҳ������Cache�ļ���
        while (false !== ($entry = $d->read()))
		{
       		if (substr($entry,0,$strlen)==$this->cachefilevar)
			{
           		if (!unlink(_CachePath_."/".$entry)) {echo "CacheĿ¼�޷�д��";exit;}
         	}
     	}
	}

	//�ж��Ƿ���Cache�����Լ��Ƿ���ҪCache
	function check()
	{
        //��������˻�����¼��ʱ�� _ReCacheTime_
        if (_ReCacheTime_+0>0)
		{
       		//���ص�ǰҳCache��������ʱ��
         	$var=@file(_CachePath_."/".$this->cachefilevar);$var=$var[0];
         	//�������ʱ�䳬�����¼��ʱ����ɾ��Cache�ļ�
       		if (time()-$var>_ReCacheTime_)
			{
           		$this->delete();$ischage=true;
         	}
  		}
        //���ص�ǰҳ��Cache
        $file=_CachePath_."/".$this->cachefile;
        //�жϵ�ǰҳCache�Ƿ���� �� Cache�����Ƿ���
        return (file_exists($file) and _CacheEnable_ and !$ischange);
	}

	//��ȡCache
	function read()
	{
    	//���ص�ǰҳ��Cache
        $file=_CachePath_."/".$this->cachefile;
        //��ȡCache�ļ�������
        if (_CacheEnable_) return readfile($file);
        else return false;
	}

	//����Cache
	function write($output)
	{
        //���ص�ǰҳ��Cache
        $file=_CachePath_."/".$this->cachefile;
        //���Cache���ܿ���
        if (_CacheEnable_)
		{
          	//�����������д��Cache�ļ�
       		$fp=@fopen($file,'w');
           	if (!@fputs($fp,$output)) {echo "ģ��Cacheд��ʧ��";exit;}
           	@fclose($fp);
           	//��������˻�����¼��ʱ�� _ReCacheTime_
          	if (_ReCacheTime_+0>0)
			{
               	//���µ�ǰҳCache��������ʱ��
             	$file=_CachePath_."/".$this->cachefilevar;
               	$fp=@fopen($file,'w');
              	if (!@fwrite($fp,time())) {echo "CacheĿ¼�޷�д��";exit;}
             	@fclose($fp);
          	}
   		}
	}
}
?>