<div class="hl_client_reviews_admin">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="page-header">
					<h4>صوتك مسموع <small>(اكتب رأيك أو قم بتحديث رأيك من الفورم بالأسفل)</small></h4>
				</div>
			</div>
		</div>
		{if $message}
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-{$message_type}" role="alert">
					{$message}
				</div>
			</div>
		</div>
		{/if}
		
		<div class="row sectionSmall">
			<div class="col-md-12">
				<form action="{$modulelink}" method="post">
					<div class="form-group">
						<label for="hl_client_review_text">رأيك بالخدمة بشكل عام</label>
						<textarea name="review" id="hl_client_review_text" class="form-control input-lg" rows="5">{if $review}{$review}{/if}</textarea>
					</div>
					<div class="checkbox">
						<label>
							<input name="show_last_name" value="1" type="checkbox"{if $show_last_name} checked{/if} />هل تسمح بعرض إسمك الأخير؟
						</label>
					</div>
					<div class="checkbox">
						<label>
							<input name="show_email" value="1" type="checkbox"{if $show_email} checked{/if} />هل تسمح بعرض بريدك الإلكتروني؟
						</label>
					</div>
					<div class="row">
						<div class="{if $review}col-xs-6{else}col-xs-12{/if}">
							<input class="btn btn-default btn-block" type="submit" name="{if $review}update{else}add{/if}" value="{if $review}تحديث{else}إضافة{/if}">
						</div>
						{if $review}
						<div class="col-xs-6">
							<input class="btn btn-primary btn-block" type="submit" name="delete" value="مسح">
						</div>
						{/if}
					</div>
				</form>
			</div>
		</div>
		{if $review}
		<div class="row">
			<div class="col-md-12">
				<p><small><strong>*تنبيه:</strong> عند تحديث رأيك, سيتم وضعه في حالة الإنتظار أولاً وسيتم تفعيله بأقرب وقت ممكن.</small></p>
			</div>
		</div>
		{/if}
	</div>
  </div>