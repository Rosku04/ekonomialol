<?php
session_start();
include('questions.php');

// Inicjalizacja sesji, jeśli nie istnieje
if (!isset($_SESSION['questionIndex'])) {
    $_SESSION['questionIndex'] = 0;
    $_SESSION['wrongAnswers'] = 0;
}

$resultMessage = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $selectedAnswer = $_POST['question' . $_SESSION['questionIndex']] ?? null;
    
    if ($action === 'check') {
        if ($selectedAnswer === null) {
            $resultMessage = "<span class='error'>Proszę wybrać odpowiedź!</span>";
        } else {
            $correctAnswer = $questions[$_SESSION['questionIndex']]['correct'];
            if ($selectedAnswer == $correctAnswer) {
                $resultMessage = "<span class='correct'>Brawo! To jest poprawna odpowiedź!</span>";
            } else {
                $_SESSION['wrongAnswers']++; // Zwiększenie licznika błędów
                $resultMessage = "<span class='incorrect'>Błędna odpowiedź. Poprawna: " . 
                                 htmlspecialchars($questions[$_SESSION['questionIndex']]['answers'][$correctAnswer]) . 
                                 ".</span>";
            }
        }
    }

    if ($action === 'next') {
        $_SESSION['questionIndex']++;
        if ($_SESSION['questionIndex'] >= count($questions)) {
            $resultMessage = "<span class='end'>Gratulacje! Ukończyłeś quiz.<br>Liczba błędnych odpowiedzi: <b>" . $_SESSION['wrongAnswers'] . "</b></span>";
            session_destroy(); // Resetujemy sesję po zakończeniu quizu
        } else {
            $resultMessage = '';
        }
    }

    if ($action === 'reset') {
        session_destroy();  // Usunięcie sesji
        session_start();    // Nowa sesja
        $_SESSION['questionIndex'] = 0;
        $_SESSION['wrongAnswers'] = 0;
        header("Location: " . $_SERVER['PHP_SELF']); // Odświeżenie strony
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Online</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Rozwiąż test</h1>

    <form method="POST">
    <?php if ($_SESSION['questionIndex'] < count($questions)): ?>
        <div class="question">
            <p><?= htmlspecialchars($questions[$_SESSION['questionIndex']]['question']) ?></p>
            <?php foreach ($questions[$_SESSION['questionIndex']]['answers'] as $i => $answer): ?>
                <input type="radio" name="question<?= $_SESSION['questionIndex'] ?>" value="<?= $i ?>" id="q<?= $_SESSION['questionIndex'] ?>a<?= $i ?>">
                <label for="q<?= $_SESSION['questionIndex'] ?>a<?= $i ?>"><?= htmlspecialchars($answer) ?></label><br>
            <?php endforeach; ?>
        </div>

        <button type="submit" name="action" value="check" class="btn">Sprawdź</button>
        <button type="submit" name="action" value="next" class="btn">Następne pytanie</button>
    <?php endif; ?>

    <!-- Przycisk resetowania testu dostępny zawsze -->
    <button type="submit" name="action" value="reset" class="btn reset-btn">Rozpocznij od nowa</button>
</form>


    <?php if (!empty($resultMessage)): ?>
        <p class="result"><?= $resultMessage ?></p>
    <?php endif; ?>
</body>
</html>
