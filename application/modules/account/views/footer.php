<div id="footer">
    <div>
        	<div id='footerlinks'>
				<ul>
					<li><a href='http://www.sf.net/projects/cuatrovientos' title='4vientos project page'>Project page</a></li>
					<li><a href='https://sourceforge.net/apps/mediawiki/cuatrovientos/index.php?title=Main_Page' title='4vientos project wiki'>4vientos wiki</a></li>
					<li><a href='http://www.cuatrovientos.org' title='Instituto Cuatrovientos home page'>Instituto Cuatrovientos</a></li>
					<li><a href='about' title='about'>About</a></li>
				</ul>		
			</div>
         <div id="copyleft">
             		4vientos &copy; <?=date('Y')?> Instituto Cuatrovientos - a3m login Copyright &copy; <?php echo date('Y'); ?> ShowNearby. All rights reservered. 
         </div>
         <div class="grid_6 omega textright">
             <?php echo sprintf(lang('website_page_rendered_in_x_seconds'), $this->benchmark->elapsed_time()); ?>
         </div>

        <div class="clear"></div>
    </div>
</div>