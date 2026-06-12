# Loyihani to'liq bitta buyruq bilan yoqish
start:
	./vendor/bin/sail up -d --remove-orphans

# Loyihani o'chirish
stop:
	./vendor/bin/sail down

# deploy
deploy:
	./vendor/bin/sail artisan app:deploy