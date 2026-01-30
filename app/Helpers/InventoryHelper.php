<?php

if (!function_exists('format_process_units')) {
    /**
     * สำหรับแสดงผลในช่อง Sell, Transfer, Spoil, Request
     * แสดงตัวย่อ B, G ต่อท้ายตัวเลข
     */
    function format_process_units($processes): string
    {
        if ($processes instanceof \Illuminate\Support\Collection) {
            $processes = $processes->toArray();
        }

        if (empty($processes) || !is_array($processes)) {
            return '-';
        }

        $output = [];
        foreach ($processes as $unit => $qty) {
            if ($qty != 0) {
                // กำหนดตัวย่อ
                $unitLower = strtolower($unit);
                $alias = '';
                if (str_contains($unitLower, 'bott')) $alias = 'B';
                elseif (str_contains($unitLower, 'glas')) $alias = 'G';
                elseif (str_contains($unitLower, 'can')) $alias = 'C';
                else $alias = " " . $unit; // ถ้าไม่ตรงกลุ่มให้เว้นวรรคแล้วโชว์ชื่อเต็ม

                $output[] = "{$qty}{$alias}";
            }
        }

        return empty($output) ? '-' : implode(' ', $output);
    }
}

if (!function_exists('format_inventory_balance')) {
    /**
     * สำหรับแสดงผลช่อง Opening และ Balance
     * รองรับการทอนหน่วย (Ratio) และแสดงตัวย่อ B, G
     */
    function format_inventory_balance($balance, $ratio = 1): string
    {
        if (empty($balance)) return '0';
        if ($balance instanceof \Illuminate\Support\Collection) {
            $balance = $balance->toArray();
        }

        $unitNames = array_keys($balance);
        
        // 1. Logic การคำนวณทอนเศษ (ปัดแก้วเป็นขวด)
        if (count($unitNames) >= 2 && $ratio > 1) {
            $bigUnitKey = $unitNames[0];
            $smallUnitKey = $unitNames[1];

            // รวมเป็นหน่วยเล็กทั้งหมดเพื่อคำนวณใหม่
            $totalSmall = ($balance[$bigUnitKey] * $ratio) + ($balance[$smallUnitKey] ?? 0);

            $finalBig = (int)($totalSmall / $ratio);
            $finalSmall = (int)($totalSmall % $ratio);

            $balance = [
                $bigUnitKey => $finalBig,
                $smallUnitKey => $finalSmall
            ];
        }

        // 2. การแสดงผลพร้อมตัวย่อ
        $output = [];
        foreach ($balance as $unit => $qty) {
            if ($qty == 0) continue;

            $unitLower = strtolower($unit);
            $alias = '';
            if (str_contains($unitLower, 'bott')) $alias = 'Bottle';
            elseif (str_contains($unitLower, 'glas')) $alias = 'Glass';
            elseif (str_contains($unitLower, 'can')) $alias = 'Can';
            else $alias = " " . $unit;

            $color = $qty < 0 ? 'text-danger' : '';
            $output[] = "<span class='{$color}'>{$qty}{$alias}</span>";
        }

        return empty($output) ? '0' : implode(' ', $output);
    }
}