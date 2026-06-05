# Loyihani to'liq bitta buyruq bilan yoqish
start:
	./vendor/bin/sail up -d
	./vendor/bin/sail npm run dev

# Loyihani o'chirish
stop:
	./vendor/bin/sail down