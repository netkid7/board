<?php 
session_start();

include "../inc/header.php";
if(!$_SESSION['_name']) {	gotoPage("../login.php"); exit;}

?>




	<div id="contents">
		<div id="contents-wrap">
			<div id="contents-title"><span>앱다운로드</span></div>
			<div id="contents-inner">
				<!-- 본문 시작 -->
				<table class="common">
					<caption></caption>
					<colgroup>
						<col width="50%" /><col width="50%" />
					</colgroup>
					<thead>
						<tr>
							<th>구분</th>
							<th>다운로드</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>iOS용 다운로드</td>
							<td><input type="submit" name="submit" value="파일 다운로드" class="file" /></td>
						</tr>
						<tr>
							<td>Android용 다운로드</td>
							<td><a href="KYUNGSAN CCTV.apk" type="application/vnd.android.package-archive" title="파일 다운로드" class="file">파일 다운로드</a></td>
						</tr>
					</tbody>
				</table>
				<!-- 본문 종료 -->
			</div>
		</div>
	</div>
</div>
</body>
</html>