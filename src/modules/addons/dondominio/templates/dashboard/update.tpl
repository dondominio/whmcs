{if $success eq true}
<script type="text/javascript">
let seconds = 5;
let countdown = function() {
    document.getElementById("countdown").innerHTML = seconds;

    if (seconds == 0) {
        history.back();
    }

    seconds --;
    setTimeout(countdown, 1000);
};

setTimeout(countdown, 1000);
</script>
<br>
{$LANG.going_back_in} <span id="countdown">5</span>.
{/if}