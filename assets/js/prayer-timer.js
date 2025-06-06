document.addEventListener("DOMContentLoaded", function () {
    if (!window.ptmData || !ptmData.timestamps) {
        console.error("Prayer time data not available");
        return;
    }

    const nowTimestamp = Math.floor(new Date().getTime() / 1000 - new Date().getTimezoneOffset() * 60);

    // Check if today is Friday (getDay() returns 5 for Friday)
    const today = new Date();
    const isFriday = today.getDay() === 5;

    // Conditionally define prayerTimes array
    const prayerTimes = [];

    // Define message that will show atop the widget - jumuah shows different text
    let message = '';

    // Always include Fajr
    prayerTimes.push({
        name: 'Fajr',
        time: ptmData.timestamps['timestamp-fajr'],
        formattedTime: ptmData.iqamah_times.fajr
    });

    // Sunrise
    prayerTimes.push({
        name: 'Sunrise',
        time: ptmData.timestamps['timestamp-sunrise'],
        formattedTime: ptmData.iqamah_times.sunrise || ''
    });

    if (isFriday) {
        // On Friday: include Jumuah first and second
        prayerTimes.push({
            name: 'Jumu\'ah',
            order: 'First',
            time: ptmData.timestamps['timestamp-jumuah_first'],
            formattedTime: ptmData.iqamah_times.jumuah_first
        });
        prayerTimes.push({
            name: 'Jumu\'ah',
            order: 'Second',
            time: ptmData.timestamps['timestamp-jumuah_second'],
            formattedTime: ptmData.iqamah_times.jumuah_second
        });
    } else {
        // Regular Zuhr on non-Fridays
        prayerTimes.push({
            name: 'Zuhr',
            time: ptmData.timestamps['timestamp-zuhr'],
            formattedTime: ptmData.iqamah_times.zuhr
        });
    }

    // Continue with Asr, Maghrib, Isha
    ['asr', 'maghrib', 'isha'].forEach(prayer => {
        prayerTimes.push({
            name: prayer.charAt(0).toUpperCase() + prayer.slice(1),
            time: ptmData.timestamps[`timestamp-${prayer}`],
            formattedTime: ptmData.iqamah_times[prayer]
        });
    });

    const lastPrayer = ptmData.timestamps['timestamp-isha'];

    if (lastPrayer > nowTimestamp) {
        const nextPrayer = prayerTimes.find(p => p.time > nowTimestamp) || prayerTimes[0];

        document.getElementById(`${nextPrayer.name}-cell`).classList.add('focus');

        function updateCountdown() {
            const now = Math.floor(new Date().getTime() / 1000 - new Date().getTimezoneOffset() * 60);
            const remaining = nextPrayer.time - now;

            if (remaining <= 0) {
                location.reload();
                return;
            }

            const hours = Math.floor(remaining / 3600);
            const minutes = Math.floor((remaining % 3600) / 60);
            const seconds = remaining % 60;
            const formattedTimeWithoutSeconds = nextPrayer.formattedTime.split(':').slice(0, 2).join(':');

            if (!nextPrayer.order){
                message = `Next Prayer: <strong>${nextPrayer.name} Iqamah</strong> at <strong>${formattedTimeWithoutSeconds}</strong>`;
            }
            else{
                nextPrayer.order === 'First' ? message = `<strong>First ${nextPrayer.name} Khutbah</strong> starts at <strong>${formattedTimeWithoutSeconds}</strong>`: message = `<strong>Second ${nextPrayer.name} Khutbah</strong> starts at <strong>${formattedTimeWithoutSeconds}</strong>`;;
            }

            document.getElementById('prayer-next').innerHTML = message;
            document.getElementById('prayer-countdown').innerHTML = `${hours}h ${minutes}m ${seconds}s`;
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    } else {
        document.getElementById('prayer-next').innerHTML =
            `There are no more prayers today.`;
        document.getElementById('prayer-countdown').classList.add('hide');

        const secondsUntilMidnight = (24 * 3600) - (nowTimestamp % (24 * 3600));
        setTimeout(() => location.reload(), secondsUntilMidnight * 1000);
    }
});
